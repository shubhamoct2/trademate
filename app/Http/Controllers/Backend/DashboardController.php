<?php

namespace App\Http\Controllers\Backend;

use App\Enums\KycStatus;
use App\Enums\TxnStatus;
use App\Enums\TxnType;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Gateway;
use App\Models\Invest;
use App\Models\LoginActivities;
use App\Models\ReferralRelationship;
use App\Models\Ticket;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use App\Models\KycInfo;

class DashboardController extends Controller
{
    //admin dashboard
    public function dashboard()
    {

        $transaction = new Transaction();
        $user = new User();
        $admin = new Admin();

        $totalDeposit = $transaction->totalDeposit();

        $totalSend = Transaction::where('status', TxnStatus::Success)->where(function ($query) {
            $query->where('type', TxnType::SendMoney);
        })->sum('amount');

        $activeUser = $user->where('status', 1)->count();

        $totalStaff = $admin->count();

        $latestUser = $user->latest()->take(5)->get();

        $latestInvest = Invest::with('schema')->take(5)->latest()->get();

        $totalGateway = Gateway::where('status', true)->count();

        $withdrawCount = Transaction::where(function ($query) {
            $query->where('type', TxnType::Withdraw)
                ->where('status', 'pending');
        })->count();

        $kycCount = KycInfo::where('status', KycStatus::Pending)->count();

        $depositCount = Transaction::where(function ($query) {
            $query->where('type', TxnType::ManualDeposit)
                ->where('status', 'pending');
        })->count();

        $totalReferral = ReferralRelationship::count();

        // ============================= Start dashboard statistics =============================================

        $schemeStatistics = Invest::whereNot('status', 'canceled')->get()->groupBy('schema.name')->map(function ($group) {
            return $group->count();
        })->toArray();

        $startDate = request()->start_date ? Carbon::createFromDate(request()->start_date) : Carbon::now()->subDays(14);
        $endDate = request()->end_date ? Carbon::createFromDate(request()->end_date) : Carbon::now();
        $dateArray = array_fill_keys(generate_date_range_array($startDate, $endDate), 0);

        $dateFilter = [request()->start_date ? $startDate : $startDate->subDays(1), $endDate->addDays(1)];

        $depositStatistics = $totalDeposit->whereBetween('created_at', $dateFilter)->get()->groupBy('day')->map(function ($group) {
            return $group->sum('amount');
        })->toArray();

        $depositStatistics = array_replace($dateArray, $depositStatistics);



        $investStatistics = $transaction->totalInvestment()->whereBetween('created_at', $dateFilter)->get()->groupBy('day')->map(function ($group) {
            return $group->sum('amount');
        })->toArray();

        $investStatistics = array_replace($dateArray, $investStatistics);

        $withdrawStatistics = $transaction->totalWithdraw()->whereBetween('created_at', $dateFilter)->get()->groupBy('day')->map(function ($group) {
            return $group->sum('amount');
        })->toArray();
        $withdrawStatistics = array_replace($dateArray, $withdrawStatistics);

        $profitStatistics = $transaction->totalProfit()->whereBetween('created_at', $dateFilter)->get()->groupBy('day')->map(function ($group) {
            return $group->sum('amount');
        })->toArray();
        $profitStatistics = array_replace($dateArray, $profitStatistics);

        // ============================= End dashboard statistics =============================================

        $browser = LoginActivities::all()->groupBy('browser')->map(function ($browser) {
            return $browser->count();
        })->toArray();
        $platform = LoginActivities::all()->groupBy('platform')->map(function ($platform) {
            return $platform->count();
        })->toArray();

        $country = User::all()->groupBy('country')->map(function ($country) {
            return $country->count();
        })->toArray();
        arsort($country);
        $country = array_slice($country, 0, 5);

        $symbol = setting('currency_symbol','global');

        /* Get pending ticket count */
        $open_tickets = Ticket::where('status', 'open')->get();

        $pending_client = 0;
        $pending_support = 0;

        foreach ($open_tickets as $ticket) {
            $last_message = $ticket->messages()->orderByDesc('created_at')->first();    
            if ($last_message) {
                if ($last_message->model == 'admin') {
                    $pending_client += 1;
                } else {
                    $pending_support += 1;
                }
            }
        }

        // total trading wallet balance
        $total_trading = User::where('status', 1)->sum('trading_balance');
        
        $data = [
            'withdraw_count' => $withdrawCount,
            'kyc_count' => $kycCount,
            'deposit_count' => $depositCount,

            'register_user' => $user->count(),
            'active_user' => $activeUser,
            'latest_user' => $latestUser,
            'latest_invest' => $latestInvest,

            'total_staff' => $totalStaff,

            'total_deposit' => $transaction->totalDeposit()->sum('amount'),
            'total_send' => $totalSend,
            'total_investment' => $transaction->totalInvestment()->sum('amount'),
            'total_withdraw' => $transaction->totalWithdraw()->sum('amount'),
            'total_referral' => $totalReferral,
            'total_trading' => $total_trading,

            'date_label' => $dateArray,
            'deposit_statistics' => $depositStatistics,
            'invest_statistics' => $investStatistics,
            'withdraw_statistics' => $withdrawStatistics,
            'profit_statistics' => $profitStatistics,

            'start_date' => isset(request()->start_date) ? $startDate : $startDate->addDays(1)->format('m/d/Y'),
            'end_date' => isset(request()->end_date) ? $endDate : $endDate->subDays(1)->format('m/d/Y'),

            'scheme_statistics' => $schemeStatistics,
            'deposit_bonus' => $transaction->totalDepositBonus(),
            'investment_bonus' => $transaction->totalInvestBonus(),
            'total_gateway' => $totalGateway,
            'total_ticket' => count($open_tickets),
            'pending_client' => $pending_client,
            'pending_support' => $pending_support,

            'browser' => $browser,
            'platform' => $platform,
            'country' => $country,
            'symbol' => $symbol,
        ];



        if (request()->ajax()) {
            $date = [
                'date_label' => $dateArray,
                'deposit_statistics' => $depositStatistics,
                'invest_statistics' => $investStatistics,
                'withdraw_statistics' => $withdrawStatistics,
                'profit_statistics' => $profitStatistics,
                'symbol' => $symbol,
            ];
            return response()->json($date);
        }

        return view('backend.dashboard', compact('data'));
    }
}
