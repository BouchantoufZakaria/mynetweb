<?php

namespace App\Console\Commands;

use App\Jobs\DailWinnersChoseAndPay;
use Illuminate\Console\Command;

class Winners extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'game:winners';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
       $dispatcher = DailWinnersChoseAndPay::dispatch();
       $this->info('Winners job dispatched successfully.');
    }
}
