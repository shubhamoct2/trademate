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
use App\Models\Ranking;

use App\Enums\TxnType;
use App\Enums\TxnStatus;

use App\Traits\NotifyTrait;

use Txn;
use Illuminate\Support\Facades\Log;

class UserRankingJob implements ShouldQueue
{
    use NotifyTrait, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $user = $this->user;

        $rankings = Ranking::where('status', '=',true)->get();

        $eligibleRanks = $rankings->reject(function ($rank) use ($user) {
            $totalEarning = $user->totalProfit();
            $totalDeposit = $user->totalDeposit();
            $totalInvest = $user->totalInvestment();
            $minimumReferral = $user->referrals->count();
            $minimumReferralDeposit = $user->referrals->sum('total_deposit');
            $minimumReferralInvest = $user->referrals->sum('total_invest');

            return in_array($rank->id, json_decode($user->rankings)) ||
                $rank->minimum_earnings > $totalEarning ||
                $rank->minimum_deposit > $totalDeposit ||
                $rank->minimum_invest > $totalInvest ||
                $rank->minimum_referral > $minimumReferral ||
                $rank->minimum_referral_deposit > $minimumReferralDeposit||
                $rank->minimum_referral_invest > $minimumReferralInvest;
        });

        if ($eligibleRanks->isNotEmpty()) {
            $maxRank = $eligibleRanks->max('minimum_earnings');
            $highestRank = $eligibleRanks->where('minimum_earnings', $maxRank)->first();

            foreach ($eligibleRanks as $rank) {
                Txn::new($rank->bonus, 0, $rank->bonus, 'system', 'Referral Bonus by ' . $rank->ranking, TxnType::Bonus, TxnStatus::Success, null, null, $user->id);
                $user->increment('commission_balance', $rank->bonus);

                if ($rank->id === $highestRank->id) {
                    $user->update([
                        'ranking_id' => $rank->id,
                        'rankings' => json_encode(array_merge(json_decode($user->rankings), [$rank->id])),
                    ]);

                    Log::info('User id='.$this->user->id.' upgraded to ranking '.$rank->id);
                }
            }
        }
    }
}
