<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\User;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

use App\Jobs\UserRankingJob;

class AutoRanking extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:ranking';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Decide user ranking automatically.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $now = Carbon::now();
        Log::info('CronJob (AutoRanking) => started at: ' . $now->format('Y-m-d H:i:s'));

        $active_users = User::where('status',true)->get();

        foreach ($active_users as $user) {
            dispatch(new UserRankingJob($user));
        }

        return Command::SUCCESS;
    }
}
