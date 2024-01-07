<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;

use App\Enums\TxnStatus;
use App\Enums\TxnType;

use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

use App\Models\AdminHistory;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
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

    private function getSummaryData($date_str) {
        $now = Carbon::now()->endOfDay();

        $start = Carbon::parse($date_str)->addDays(1)->startOfDay();
        $end = $start->copy()->endOfDay();

        if ($end->gt($now)) {
            return [
                'main_wallet' => 0,
                'trading_wallet' => 0,
                'profit_wallet' => 0,
                'profit_distribution' => 0,
                'commission_wallet' => 0,
                'commission_distribution' => 0,
                'withdraw_request' => 0,
                'withdraw_processed' => 0,
            ];
        }

        $admin_history = AdminHistory::whereBetween('updated_at', array($start, $end))->first();

        if ($admin_history) {
            $data = $admin_history->data;

            return [
                'main_wallet' => $data['main_wallet'],
                'trading_wallet' => $data['trading_wallet'],
                'profit_wallet' => $data['profit_wallet'],
                'commission_wallet' => $data['commission_wallet'],
                'profit_distribution' => $data['profit_share'],
                'commission_distribution' => $data['commission_share'],
                'withdraw_request' => $data['withdraw_request'],
                'withdraw_processed' => $data['withdraw_processed'],
            ];
        }

        return [
            'main_wallet' => 0,
            'trading_wallet' => 0,
            'profit_wallet' => 0,
            'profit_distribution' => 0,
            'commission_wallet' => 0,
            'commission_distribution' => 0,
            'withdraw_request' => 0,
            'withdraw_processed' => 0,
        ];
    }

    private function getSummary($year, $month) {
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
                        'name' => __('Main Wallets Total'),
                        'class' => '',
                    ],
                    'trading_wallet' => [
                        'name' => __('Trading Wallets Total'),
                        'class' => '',
                    ],
                    'profit_wallet' => [
                        'name' => __('Profit Wallets Total'),
                        'class' => '',
                    ],
                    'profit_distribution' => [
                        'name' => __('Profit Wallets Distribution'),
                        'class' => '',
                    ],
                    'commission_wallet' => [
                        'name' => __('Commission Wallets Total'),
                        'class' => '',
                    ],
                    'commission_distribution' => [
                        'name' => __('Commission Wallets Distribution'),
                        'class' => '',
                    ],
                    'withdraw_request' => [
                        'name' => __('Withdraws Request Total'),
                        'class' => '',
                    ],
                    'withdraw_processed' => [
                        'name' => __('Withdraws Processed Total'),
                        'class' => '',
                    ],
                ];
            }

            $day_summary = $this->getSummaryData($date_str);

            $summary_month[$week_number]['list']['main_wallet'][$weekday_str[$date->dayOfWeekIso - 1]] = number_format($day_summary['main_wallet'], 2);
            $summary_month[$week_number]['list']['trading_wallet'][$weekday_str[$date->dayOfWeekIso - 1]] = number_format($day_summary['trading_wallet'], 2);
            $summary_month[$week_number]['list']['profit_wallet'][$weekday_str[$date->dayOfWeekIso - 1]] = number_format($day_summary['profit_wallet'], 2);
            $summary_month[$week_number]['list']['profit_distribution'][$weekday_str[$date->dayOfWeekIso - 1]] = number_format($day_summary['profit_distribution'], 2);
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
                        $item['week'] = number_format(floatval($this->getSumValue($item)), 2);
                        // if ($start_of_month->gt($now)) {
                        //     $item['week'] = '';
                        // } else {
                        //     $item['week'] = number_format($this->getSumValue($item), 2);
                        // }
                    }
                }
            }

            $summary_week['date'] = $this->getDaysInWeek($year, $week_number);
        }

        return $summary_month;
    }

    public function list(Request $request) {
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
        return view('backend.history.index', compact('setting', 'summary_month'));
    }
}
