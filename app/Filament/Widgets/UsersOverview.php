<?php

namespace App\Filament\Widgets;

use App\Models\Draw;
use App\Models\User;
use App\Models\UserSession;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UsersOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make("Today Actives" , $this->calculateTodayActives())
                ->icon('heroicon-o-users'),
            Stat::make("Total Users" , $this->calculateUsersCount())
                ->icon('heroicon-o-user-group'),
            Stat::make("Total Amount Spent" , $this->countTotalAmountSpent())
                ->icon('heroicon-o-currency-dollar'),
        ];
    }


    private function calculateTodayActives() : string {
        $today = Carbon::today()->toDateString();
        $sessions = UserSession::where('login_at'  , $today)->count();
        return $sessions > 0 ? str($sessions) : '0';
    }

    private function calculateUsersCount() : string {
        $users = User::count();
        return $users > 0 ? str($users) : '0';
    }

    private function countTotalAmountSpent() : string {
        $draws = Draw::all()->select('total_amount')->where('status' , 'completed')->sum('total_amount');
        return $draws > 0 ? str($draws) : '0';
    }
}
