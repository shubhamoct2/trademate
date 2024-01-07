<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Transaction;
use App\Models\User;

use App\Enums\TxnType;
use App\Enums\TxnStatus;

use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DepositCancel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deposit:cancel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancel pending deposit request older than 1 hour.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $now = Carbon::now();
        // Log::info('CronJob (DepositCancel) => started at: ' . $now->format('Y-m-d H:i:s'));

        $pending_deposit_count = Transaction::where('status', TxnStatus::Pending)
            ->where('type', TxnType::Deposit)                
            ->whereDate('created_at', '<', Carbon::now('Europe/Berlin')->subHours(1))
            ->count();

        if ($pending_deposit_count > 0) {
            Transaction::where('status', TxnStatus::Pending)
                ->where('type', TxnType::Deposit)                
                ->whereDate('created_at', '<', Carbon::now('Europe/Berlin')->subHours(1))
                ->update(['status' => TxnStatus::Failed]);

            Log::info('CronJob (DepositCancel) => cancelled ' . $pending_deposit_count . ' deposit');
        }

        // Log::info('CronJob (DepositCancel) => finished at: ' . $now->format('Y-m-d H:i:s'));

        return Command::SUCCESS;
    }
}
