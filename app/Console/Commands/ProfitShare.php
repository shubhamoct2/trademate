<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Transaction;
use App\Models\User;
use App\Models\AutoTask;

use App\Enums\TxnType;
use App\Enums\AutoTaskType;
use App\Enums\TxnStatus;

use App\Traits\NotifyTrait;

use App\Jobs\SendProfitShareJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ProfitShare extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'profit:share';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send profit to all users according to their share rate.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $now = Carbon::now();

        // Log::info('CronJob (ProfitShare) => started at: ' . $now->format('Y-m-d H:i:s'));

        $autoTaskList = AutoTask::where('type', AutoTaskType::ProfitShare)
            ->where('status', TxnStatus::Pending)
            ->whereDate('created_at', '<', Carbon::now('Europe/Berlin'))
            ->get();

        if (!is_null($autoTaskList) && count($autoTaskList) > 0) {
            foreach ($autoTaskList as $task) {
                $details = json_decode($task->data);

                if ($details->method == 'manual') {
                    $this->sendProfit($task, $details->amount);
                } else {
                    $dateTime = Carbon::parse($details->datetime)->timezone('Europe/Berlin');
                    if ($dateTime->isPast()) {
                        $this->sendProfit($task, $details->amount);
                    }
                }
            }
        } else {
            // Log::info('CronJob (ProfitShare) => not found any task for profit share.');
        }

        return Command::SUCCESS;
    }

    private function sendProfit(AutoTask $task, $amount) {
        Log::info('CronJob (ProfitShare) => send profit: ' . $task->id . ' start! details => ' . $task->data);

        $total_profit = floatval($amount);
        $total_trading = User::where('status', 1)->sum('trading_balance');
        $active_user_list = User::where('status', 1)->get();

        foreach ($active_user_list as $user) {
            if ($user->trading_balance > 0) {
                $user_profit = round(($total_profit * floatval($user->trading_balance))  / floatval($total_trading), 2);

                dispatch(new SendProfitShareJob($user, $user_profit));
            }
        }

        $task->update([
            'status' => TxnStatus::Success
        ]);

        Log::info('CronJob (ProfitShare) => send profit: ' . $task->id . ' end!');
    }
}
