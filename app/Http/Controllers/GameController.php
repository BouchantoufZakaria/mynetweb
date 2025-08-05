<?php

namespace App\Http\Controllers;


use App\Models\User;
use App\Models\UserSession;
use App\Utils\NumbersUtils;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\GameService;
use Illuminate\Support\Facades\Cache;
use Laravel\Sanctum\HasApiTokens;
use Random\RandomException;

class GameController extends Controller
{

    use NumbersUtils;

    function login(Request $request): \Illuminate\Http\JsonResponse
    {
        try {

            $uuid = $request->input("uuid");
            $phoneNumber = $request->input("phone_number");
            $username = $request->input("username");

            if (!$uuid || !$phoneNumber || !$username) {
                return \response()->json([
                    "message" => "Missing required parameters",
                ], 400);
            }

            $gameService = new GameService();
            $phoneNumber = $this->uniformAlgerianNumbers($phoneNumber);

            // is there is a user with the same uuid and phone number ?
            $result = User::where('uuid', $uuid)
                ->where('phone_number', $phoneNumber)
                ->first();


            if ($result) {
                return response()->json([
                    "access_token" => $result->access_token,
                ]);
            } else {
                $token = $gameService->createNewAccessTokenAndUpdateOrCreateUser(
                    $uuid,
                    $phoneNumber,
                    $username
                );

                return response()->json([
                    "access_token" => $token,
                ]);
            }
        } catch (\Throwable $e) {
            \Log::error("Error updating user information: " . $e->getMessage());
            return response()->json([
                "message" => "Oops! Something went wrong.",
//                "error" => $e->getMessage(), enable of debug only
            ], 500);
        }

    }


    function updateUserInformation(Request $request): JsonResponse
    {
        try {
            $username = $request->input("name");
            $fcmToken = $request->input("fcm_token");
            $phoneNumber = $request->input("phone_number");
            $bearerToken = $request->bearerToken();


            if (!$bearerToken) {
                return \response()->json([
                    "message" => "Access token is required",
                ], 401);
            }


            if (!$username && !$fcmToken && !$phoneNumber) {
                return \response()->json([
                    "message" => "Missing required parameters",
                ], 400);
            }

            $user = User::where('access_token', $bearerToken)->first();
            if (!$user) {
                return \response()->json([
                    "message" => "User not found",
                ], 404);
            }

            // Update user information
            if ($username) {
                $user->username = $username;
            }
            if ($fcmToken) {
                $user->fcm_token = $fcmToken;
            }
            if ($phoneNumber) {
                $phoneNumber = $this->uniformAlgerianNumbers($phoneNumber);
                $user->phone_number = $phoneNumber;
            }

            if ($user->isDirty()) {
                $user->save();
            }

            return \response()->json([
                "message" => "User information updated successfully",
                "user" => [
                    "username" => $user->username,
                    "fcm_token" => $user->fcm_token,
                    "phone_number" => $user->phone_number,
                ]
            ]);

        } catch (\Throwable $e) {
            \Log::error("Error updating user information: " . $e->getMessage());
            return \response()->json([
                "message" => "Oops! Something went wrong.",
//                "error" => $e->getMessage(), enable of debug only
            ]);
        }
    }


    function getUserInformation(): JsonResponse
    {
        $barerToken = request()->bearerToken();

        $user = User::where('access_token', $barerToken)->first();
        if (!$user) {
            return \response()->json([
                "message" => "User not found",
            ], 404);
        }

        $hasWinBefore = $user->last_win_draw_id != null;
        $eligible = true;

        if ($hasWinBefore) {
            $eligible = !$hasWinBefore && !($user->lastWinDraw->update_at > now()->subDays(30));
        }

        $data = [
            "id" => $user->id,
            "uuid" => $user->uuid,
            "username" => $user->username,
            "phone_number" => $user->phone_number,
            "access_token" => $user->access_token,
            "eligible" => $eligible
        ];


        return \response()->json([
            "user" => $data
        ]);
    }

    function createUserSession(): JsonResponse
    {
        try {
            $bearerToken = request()->bearerToken();

            if (!$bearerToken) {
                return \response()->json([
                    "message" => "Access token is required",
                ], 401);
            }

            $user = User::where('access_token', $bearerToken)->select('id')->first();
            if (!$user) {
                return \response()->json([
                    "message" => "User not found",
                ], 404);
            }

            // get session if existed
            $today = Carbon::today() ;
            $todayString = $today->toDateString() ;
            $session = UserSession::where('login_at' , $todayString)
                ->where('user_id', $user->id)
                ->exists() ;

            if ($session) {
                return \response()->json([
                    "message" => "Session already exists for today"
                ]);
            }


            UserSession::create([
                'user_id' => $user->id,
                'login_at' => Carbon::today(),
            ]);

            return \response()->json([
                "message" => "Session created successfully",
            ]);

        } catch (\Throwable $e) {
            return \response()->json([
                "message" => "Oops! Something went wrong." ,/* "error" => $e->getMessage()*/ ] , 500);
        }
    }

    function getLastWinnersDraws()
    {
        try {
            $oneDayBefore = now()->subDay(1)->toDateString();

            // Cache key for this specific draw date
            $cacheKey = "yesterday_draw_winners_{$oneDayBefore}";

            $draw = Cache::remember($cacheKey, 3600, function () use ($oneDayBefore) {
                return \App\Models\Draw::where('date', $oneDayBefore)
                    ->with('userDraws.user')
                    ->first();
            });

            if (!$draw) {
                return response()->json([
                    "message" => "No draws found for yesterday",
                ], 404);
            }

            $userDraws = $draw->userDraws;

            if ($userDraws->isEmpty()) {
                return response()->json([
                    "message" => "No winners found for yesterday's draw",
                ], 404);
            }


            $winners = $userDraws->map(function ($userDraw) {
                $user = $userDraw->user;
                $maskedPhoneNumber = $this->hidePhoneNumber($this->formatNumbersForLocalUses($user->phone_number));

                return [
                    "id" => $user->id,
                    "username" => $user->username,
                    "phone_number" => $maskedPhoneNumber, // using accessor
                    "amount" => $userDraw->amount ,
                ];

            });

            return response()->json([
                "winners" => $winners,
                "draw_date" => $draw->date,
                "total_amount" => $draw->total_amount
            ]);
        } catch (\Throwable $e) {
            \Log::error("Error fetching last winners: " . $e->getMessage());

            return response()->json([
                "message" => "Unexpected error occurred. Please try again."
            ], 500);
        }
    }

}
