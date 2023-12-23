<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\User;
use App\Models\Transaction;
use App\Models\LevelReferral;

use App\Enums\TxnType;
use App\Enums\TxnStatus;

use App\Traits\NotifyTrait;

use Txn;
use Illuminate\Support\Facades\Log;

class SendProfitShareJob implements ShouldQueue
{
    use NotifyTrait, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;
    public $amount;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, $amount)
    {
        $this->user = $user;
        $this->amount = $amount;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('User id='.$this->user->id.' sent profit $'.$this->amount);

        $this->user->increment('profit_balance', $this->amount);

        $transaction = Txn::new (
            $this->amount, 
            0, 
            $this->amount, 
            'system', 
            __('Manual Profit Distribution by System'),
            TxnType::ProfitShare, 
            TxnStatus::Success, 
            null, 
            $this->amount, 
            $this->user->id,
            $this->user->id,
            'Admin'
        );

        if (setting('site_referral', 'global') == 'level' && setting('profit_level')) {
            $level = LevelReferral::where('type', 'profit')->max('the_order') + 1;
            creditReferralBonus($transaction->user, 'profit', $this->amount, $level);
        }

        $shortcodes = [
            '[[full_name]]' => $transaction->user->full_name,
            '[[txn]]' => $transaction->tnx,
            '[[method_name]]' => strtoupper($transaction->method),
            '[[commission_amount]]' =>  $transaction->final_amount . setting('site_currency', 'global'),
            '[[site_title]]' => setting('site_title', 'global'),
            '[[site_url]]' => route('home'),
            '[[message]]' => '',
            '[[status]]' => 'approved',
        ];
    
        $this->pushNotify('received_profit', $shortcodes, route('user.transactions'), $transaction->user->id);
    }
}
