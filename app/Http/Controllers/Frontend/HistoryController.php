<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;

use App\Models\Wallet;
use App\Enums\WalletStatus;
use App\Traits\ImageUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Hash;
use Auth;
use Carbon\Carbon;
use App\Enums\TxnStatus;
use App\Enums\TxnType;

class HistoryController extends Controller
{
    use ImageUpload;

    private function getDaysInWeek($year, $week_number) {
        $now = Carbon::now();
        $now->setISODate($year, $week_number); 

        $start = $now->startOfWeek();
        $days = [];

        for ($day=0; $day<7; $day++) {
            $days[] = $start->format('Y-m-d');
            $start->addDays(1);            
        }

        return $days;
    }

    private function getSummary($year, $month) {
        // dd ($year, $month);

        $now = Carbon::now();

        $current_year = $now->year;
        $current_month = $now->month;
        $current_day = $now->day;
        $current_weekday = $now->dayOfWeekIso;

        $weekday_str = [
            'monday',
            'tuesday',
            'wednesday',
            'thursday',
            'friday',
            'saturday',
            'sunday',
        ];

        $start_of_month = Carbon::parse($year . '-' . $month . '-1');
        $end_of_month = $start_of_month->copy()->endOfMonth();
        $end_day = ($current_year == $year && $current_month == $month) ? $current_day : $end_of_month->day;

        $summary_month = [];
        for ($day = 1; $day <= $end_day; $day++) {
            $date_str = $year . '-' . $month . '-' . $day;
            $date = Carbon::parse($date_str);
            $week_number = $date->weekOfYear;

            if (!isset($summary_month[$week_number])) {
                $summary_month[$week_number] = [];
            }

            if (!isset($summary_month[$week_number]['date'])) {
                $summary_month[$week_number]['date'] = [];
            }

            if (!isset($summary_month[$week_number]['list'])) {
                $summary_month[$week_number]['list'] = [
                    'main_wallet' => [
                        'name' => __('Main Wallet'),
                        'class' => '',
                    ],
                    'trading_wallet' => [
                        'name' => __('Trading Wallet'),
                        'class' => '',
                    ],
                    'profit_wallet' => [
                        'name' => __('Profit Wallet'),
                        'class' => '',
                    ],
                    'profit_distribution' => [
                        'name' => __('Profit Wallet Distribution'),
                        'class' => '',
                    ],
                    'profit_share' => [
                        'name' => __('Profit Percentages'),
                        'class' => '',
                    ],
                    'commission_wallet' => [
                        'name' => __('Commission Wallet'),
                        'class' => '',
                    ],
                    'commission_distribution' => [
                        'name' => __('Commission Wallet Distribution'),
                        'class' => '',
                    ],
                    'withdraw_request' => [
                        'name' => __('Withdraws Request'),
                        'class' => '',
                    ],
                    'withdraw_processed' => [
                        'name' => __('Withdraws Processed'),
                        'class' => '',
                    ],
                ];
            }

            $day_summary = $this->getSummaryData($date_str);

            $summary_month[$week_number]['list']['main_wallet'][$weekday_str[$date->dayOfWeekIso - 1]] = number_format($day_summary['main_wallet'], 2);
            $summary_month[$week_number]['list']['trading_wallet'][$weekday_str[$date->dayOfWeekIso - 1]] = number_format($day_summary['trading_wallet'], 2);
            $summary_month[$week_number]['list']['profit_wallet'][$weekday_str[$date->dayOfWeekIso - 1]] = number_format($day_summary['profit_wallet'], 2);
            $summary_month[$week_number]['list']['profit_distribution'][$weekday_str[$date->dayOfWeekIso - 1]] = number_format($day_summary['profit_distribution'], 2);
            $summary_month[$week_number]['list']['profit_share'][$weekday_str[$date->dayOfWeekIso - 1]] = number_format($day_summary['profit_share'], 2);
            $summary_month[$week_number]['list']['commission_wallet'][$weekday_str[$date->dayOfWeekIso - 1]] = number_format($day_summary['commission_wallet'], 2);
            $summary_month[$week_number]['list']['commission_distribution'][$weekday_str[$date->dayOfWeekIso - 1]] = number_format($day_summary['commission_distribution'], 2);
            $summary_month[$week_number]['list']['withdraw_request'][$weekday_str[$date->dayOfWeekIso - 1]] = number_format($day_summary['withdraw_request'], 2);
            $summary_month[$week_number]['list']['withdraw_processed'][$weekday_str[$date->dayOfWeekIso - 1]] = number_format($day_summary['withdraw_processed'], 2);
        }
        
        foreach ($summary_month as $week_number => &$summary_week) {
            foreach ($summary_week['list'] as $key => &$item) {
                if (!isset($item['week'])) {                    
                    if ($key == 'main_wallet' || 
                        $key == 'trading_wallet' || 
                        $key == 'profit_wallet' || 
                        $key == 'commission_wallet' ||
                        $key == 'profit_share') {
                        $item['week'] = number_format(floatval($this->getLastValue($item)), 2);
                    } else {
                        if ($start_of_month->gt($now)) {
                            $item['week'] = '';
                        } else {
                            $item['week'] = number_format(floatval($this->getSumValue($item)), 2);
                        }
                    }
                }
            }

            $summary_week['date'] = $this->getDaysInWeek($year, $week_number);
        }

        return $summary_month;
    }

    private function getSummaryData($date_str) {
        $user = Auth::user();

        $now = Carbon::now()->endOfDay();
        $start = Carbon::parse($date_str)->startOfDay();
        $end = $start->copy()->endOfDay();

        if ($end->gt($now)) {
            return [
                'main_wallet' => '',
                'trading_wallet' => '',
                'profit_wallet' => '',
                'profit_distribution' => '',
                'commission_wallet' => '',
                'commission_distribution' => '',
                'withdraw_request' => '',
                'withdraw_processed' => '',
                'profit_share' => '',
            ];
        }

        $deposit = $user->transaction()->where('status', TxnStatus::Success)
            ->where('type', TxnType::Deposit)                
            ->whereBetween('updated_at', array($start, $now))
            ->sum('pay_amount');
        
        $transfer_to_main = $user->transaction()->where('status', TxnStatus::Success)
            ->where('type', TxnType::Exchange)  
            ->whereIn('method', [4, 8, 12])            
            ->whereBetween('updated_at', array($start, $now))
            ->sum('amount');

        $transfer_from_main = $user->transaction()->where('status', TxnStatus::Success)
            ->where('type', TxnType::Exchange)
            ->whereIn('method', [2])
            ->whereBetween('updated_at', array($start, $now))
            ->sum('amount');
            
        $withdraw_processed = $user->transaction()->where('status', TxnStatus::Success)
            ->where('type', TxnType::Withdraw)                
            ->whereBetween('updated_at', array($start, $now))
            ->sum('pay_amount');

        $transfer_to_trading = $user->transaction()->where('status', TxnStatus::Success)
            ->where('type', TxnType::Exchange)  
            ->whereIn('method', [2, 6, 14])            
            ->whereBetween('updated_at', array($start, $now))
            ->sum('amount');

        $transfer_from_trading = $user->transaction()->where('status', TxnStatus::Success)
            ->where('type', TxnType::Exchange)  
            ->whereIn('method', [8])           
            ->whereBetween('updated_at', array($start, $now))
            ->sum('amount');

        $transfer_from_profit = $user->transaction()->where('status', TxnStatus::Success)
            ->where('type', TxnType::Exchange)  
            ->whereIn('method', [4, 6])           
            ->whereBetween('updated_at', array($start, $now))
            ->sum('amount');

        $profit_share = $user->transaction()->where('status', TxnStatus::Success)
            ->where('type', TxnType::ProfitShare)  
            ->whereBetween('updated_at', array($start, $now))
            ->sum('amount');

        $transfer_from_commission = $user->transaction()->where('status', TxnStatus::Success)
            ->where('type', TxnType::Exchange)  
            ->whereIn('method', [12, 14])           
            ->whereBetween('updated_at', array($start, $now))
            ->sum('amount');
        
        $commision_share = $user->transaction()->where('status', TxnStatus::Success)
            ->where('type', TxnType::SendCommission)  
            ->whereBetween('updated_at', array($start, $now))
            ->sum('amount');

        $profit_share_day = $user->transaction()->where('status', TxnStatus::Success)
            ->where('type', TxnType::ProfitShare)  
            ->whereBetween('updated_at', array($start, $end))
            ->sum('amount');

        $commision_share_day = $user->transaction()->where('status', TxnStatus::Success)
            ->where('type', TxnType::SendCommission)  
            ->whereBetween('updated_at', array($start, $end))
            ->sum('amount');

        $withdraw_request_day = $user->transaction()
            ->where('type', TxnType::Withdraw)  
            ->whereBetween('updated_at', array($start, $end))
            ->sum('amount');

        return [
            'main_wallet' => $user->balance - $deposit - $transfer_to_main + $transfer_from_main + $withdraw_processed,
            'trading_wallet' => $user->trading_balance - $transfer_to_trading + $transfer_from_trading,
            'profit_wallet' => $user->profit_balance - $profit_share + $transfer_from_profit,
            'profit_distribution' => $profit_share_day,
            'commission_wallet' => $user->commission_balance - $commision_share + $transfer_from_commission,
            'commission_distribution' => $commision_share_day,
            'withdraw_request' => $withdraw_request_day,
            'withdraw_processed' => $withdraw_processed,
            'profit_share' => 0,
        ];
    }

    private function getLastValue($list) {
        $value = null;

        foreach ($list as $key => $item) {
            if ($key != 'name' && $key != 'class') {
                $value = $item;
            }
        }

        return $value;
    }

    private function getSumValue($list) {
        $value = 0;

        foreach ($list as $key => $item) {
            if ($key != 'name' && $key != 'class') {
                $value += $item;
            }
        }

        return $value;
    }

    public function show(Request $request)
    {
        $input = $request->all();

        if (!isset($input['year']) || !isset($input['month'])) {
            $date = Carbon::now();             
        } else {
            $date = Carbon::parse($input['year'] . '-' . $input['month'] . '-1');
        }

        $now = Carbon::now();       

        $setting = [
            'range' => [
                'year' => range($now->year, 2000),
                'month' => range(1, 12),
            ],
            'year' => $date->year,
            'month' => $date->month,
        ];

        $summary_month = $this->getSummary($date->year, $date->month);

        // dd ($summary_month);
        return view('frontend::user.history.index', compact('setting', 'summary_month'));
    }
}
