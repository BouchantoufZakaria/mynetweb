<?php

namespace App\Jobs;

use App\Models\Draw;
use App\Models\User;
use App\Models\UserDraw;
use App\Models\UserSession;
use App\Services\ChargilyService;
use App\Utils\NumbersUtils;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DailWinnersChoseAndPay implements ShouldQueue
{
    use Queueable;
    use NumbersUtils;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     * @throws ConnectionException
     * @throws \Throwable
     */
    public function handle(): void
    {

        $today = Carbon::today();
        $totalDailyAmount = config('game.total_daily_amount');
        $winnersCount = config('game.daily_draws');
        $amountPerUser = intval($totalDailyAmount / $winnersCount);

        Log::info("DailWinnersChoseAndPay job started with parameters:
            totalDailyAmount: $totalDailyAmount,
            winnersCount: $winnersCount,
            amountPerUser: $amountPerUser");


        $eligibleUsers = $this->getEligibleUsers($today);




        if ($eligibleUsers->count() < $winnersCount) {
            \Log::warning("Not enough eligible users for draw.");
            return;
        }

        $selectedUsers = $eligibleUsers->shuffle()->take($winnersCount);

        DB::beginTransaction();
        try {
            $draw = Draw::create([
                'date' => $today,
                'status' => 'pending',
                'total_amount' => $totalDailyAmount
            ]);

            foreach ($selectedUsers as $user) {
                UserDraw::create([
                    'session_id' => $user->sessions->first()->id,
                    'user_id' => $user->id,
                    'amount' => $amountPerUser,
                    'draw_id' => $draw->id,
                    'status' => 'pending'
                ]);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error("Draw creation failed: " . $e->getMessage());
            return;
        }

        $this->payWinners($selectedUsers, $draw, $amountPerUser);
    }

    private function getEligibleUsers(Carbon $today): Collection
    {
        return User::whereHas('sessions', function ($query) use ($today) {
            $query->whereDate('login_at', $today->toDateString());
        })
            ->where(function ($query) {
                $query->whereDoesntHave('lastWinDraw')
                    ->orWhereHas('lastWinDraw', function ($q) {
                        $q->where('updated_at', '<', now()->subDays(30));
                    });
            })
            ->whereNotNull('phone_number')
            ->get();

    }

    /**
     * @throws ConnectionException
     */
    private function payWinners(Collection $users, Draw $draw, int $amountPerUser): void
    {
        $chargilyService = app(ChargilyService::class);
        $token = $chargilyService->loginToUserAccount();

        if (!$token) {
            \Log::error("Chargily login failed.");
            return;
        }

        foreach ($users as $user) {
            $userDraw = UserDraw::where('user_id', $user->id)
                ->where('draw_id', $draw->id)
                ->first();

            try {
                $chargilyService->sendPaymentRequest($token, $this->formatNumbersForLocalUses($user->phone_number), $amountPerUser);
                $userDraw?->update(['status' => 'completed']);
                $draw->update(['status' => 'completed']);
            } catch (\Throwable $e) {
                \Log::error("Chargily payment failed for user {$user->id}: " . $e->getMessage());
                $userDraw?->update(['status' => 'cancelled']);
            }
        }
    }

}
