<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Enums\TxnType;
use App\Enums\TxnStatus;

use App\Models\User;
use App\Models\Transaction;
use App\Models\AdminHistory;

use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SaveHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'save:history';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Save summary data for history.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $now = Carbon::now();

        Log::info('CronJob (SaveHistory) => started at: ' . $now->format('Y-m-d H:i:s'));

        $startDateTime = $now->copy()->addDays(-1)->startOfDay();
        $endDateTime = $startDateTime->copy()->endOfDay();

        $main_wallet = User::where('status', 1)->sum('balance');
        $trading_wallet = User::where('status', 1)->sum('trading_balance');
        $profit_wallet = User::where('status', 1)->sum('profit_balance');
        $commission_wallet = User::where('status', 1)->sum('commission_balance');

        $profit_share = Transaction::where('status', TxnStatus::Success)
            ->where('type', TxnType::ProfitShare)  
            ->whereBetween('updated_at', array($startDateTime, $endDateTime))
            ->sum('amount');

        $commission_share = Transaction::where('status', TxnStatus::Success)
            ->where(function ($query) {
                $query->where('type', TxnType::SendCommission)
                    ->orWhere('type', TxnType::Referral)
                    ->orWhere('type', TxnType::Bonus);;
            })
            ->whereBetween('updated_at', array($startDateTime, $endDateTime))
            ->sum('amount');

        $withdraw_request = Transaction::where('type', TxnType::Withdraw)  
            ->whereBetween('updated_at', array($startDateTime, $endDateTime))
            ->sum('amount');
            
        $withdraw_processed = Transaction::where('status', TxnStatus::Success)
            ->where('type', TxnType::Withdraw)  
            ->whereBetween('updated_at', array($startDateTime, $endDateTime))
            ->sum('amount');

        $adminHistory = AdminHistory::create([
            'data' => [
                'main_wallet' => floatval(number_format($main_wallet, 2)),
                'trading_wallet' => floatval(number_format($trading_wallet, 2)),
                'profit_wallet' => floatval(number_format($profit_wallet, 2)),
                'commission_wallet' => floatval(number_format($commission_wallet, 2)),
                'profit_share' => floatval(number_format($profit_share, 2)),
                'commission_share' => floatval(number_format($commission_share, 2)),
                'withdraw_request' => floatval(number_format($withdraw_request, 2)),
                'withdraw_processed' => floatval(number_format($withdraw_processed, 2)),
            ]
        ]);

        Log::info('CronJob (SaveHistory) => end! history: ' . json_encode($adminHistory->data));

        return Command::SUCCESS;
    }
}
