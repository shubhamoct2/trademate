<?php

use App\Enums\TxnStatus;
use App\Enums\TxnType;
use App\Models\Gateway;
use Carbon\Carbon;

if (! function_exists('isActive')) {
    function isActive($route, $parameter = null)
    {

        if (null != $parameter && request()->url() === route($route, $parameter)) {
            return 'active';
        }
        if (null == $parameter && is_array($route)) {
            foreach ($route as $value) {
                if (Request::routeIs($value)) {
                    return 'show';
                }
            }
        }
        if (null == $parameter && Request::routeIs($route)) {
            return 'active';
        }

    }
}

if (! function_exists('tnotify')) {
    function tnotify($type, $message)
    {
        session()->flash('tnotify', [
            'type' => $type,
            'message' => $message,
        ]);
    }
}

if (! function_exists('setting')) {
    function setting($key, $section = null, $default = null)
    {
        if (is_null($key)) {
            return new \App\Models\Setting();
        }

        if (is_array($key)) {

            return \App\Models\Setting::set($key[0], $key[1]);
        }

        $value = \App\Models\Setting::get($key, $section, $default);

        return is_null($value) ? value($default) : $value;
    }
}

if (! function_exists('oldSetting')) {

    function oldSetting($field, $section)
    {
        return old($field, setting($field, $section));
    }
}

if (! function_exists('settingValue')) {

    function settingValue($field)
    {
        return \App\Models\Setting::get($field);
    }
}

if (! function_exists('getPageSetting')) {

    function getPageSetting($key)
    {
        return \App\Models\PageSetting::where('key', $key)->first()->value;
    }
}

if (! function_exists('curl_get_file_contents')) {

    function curl_get_file_contents($URL)
    {
        $c = curl_init();
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_URL, $URL);
        $contents = curl_exec($c);
        curl_close($c);

        if ($contents) {
            return $contents;
        }

        return false;

    }
}

if (! function_exists('getCountries')) {

    function getCountries()
    {
        return json_decode(file_get_contents(resource_path().'/json/CountryCodes.json'), true);
    }
}

if (! function_exists('getJsonData')) {

    function getJsonData($fileName)
    {
        return file_get_contents(resource_path()."/json/$fileName.json");
    }
}

if (! function_exists('getTimezone')) {
    function getTimezone()
    {
        $timeZones = json_decode(file_get_contents(resource_path().'/json/timeZone.json'), true);

        return array_values(Arr::sort($timeZones, function ($value) {
            return $value['name'];
        }));
    }
}

if (! function_exists('getIpAddress')) {
    function getIpAddress()
    {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        } elseif (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        } else {
            $ipaddress = 'UNKNOWN';
        }

        return $ipaddress;
    }
}

if (! function_exists('getLocation')) {
    function getLocation()
    {
        $clientIp = request()->ip();
        $ip = $clientIp == '127.0.0.1' ||  $clientIp == '::1' ? '185.108.128.54' : $clientIp;
        $location = json_decode(curl_get_file_contents('http://ip-api.com/json/'.$ip), true);

        if (!is_null($location)) {
            $currentCountry = collect(getCountries())->first(function ($value, $key) use ($location) {
                return $value['code'] == $location['countryCode'];
            });

            $location = [
                'country_code' => $currentCountry['code'],
                'name' => $currentCountry['name'],
                'dial_code' => $currentCountry['dial_code'],
                'ip' => $location['query'] ?? [],
            ];
        } else {
            $location = [
                'country_code' => '',
                'name' => '',
                'dial_code' => '',
                'ip' => [],
            ];
        }

        return new \Illuminate\Support\Fluent($location);
    }
}

if (! function_exists('carbonInstance')) {
    function carbonInstance($dataTime): Carbon
    {
        return Carbon::create($dataTime->toString());
    }
}

if (! function_exists('gateway_info')) {
    function gateway_info($code)
    {
        $info = Gateway::where('gateway_code', $code)->first();

        return json_decode($info->credentials);
    }
}

if (! function_exists('plugin_active')) {
    function plugin_active($name)
    {
        $plugin = \App\Models\Plugin::where('name', $name)->where('status', true)->first();
        if (! $plugin) {
            $plugin = \App\Models\Plugin::where('type', $name)->where('status', true)->first();
        }

        return $plugin;
    }
}

if (! function_exists('default_plugin')) {
    function default_plugin($type)
    {
        return \App\Models\Plugin::where('type', $type)->where('status', 1)->first('name')?->name;
    }
}

if (! function_exists('br2nl')) {
    function br2nl($input)
    {
        return preg_replace('/<br\\s*?\/??>/i', '', $input);
    }
}

if (! function_exists('safe')) {
    function safe($input)
    {
        if (! env('APP_DEMO', false)) {
            return $input;
        }

        if (filter_var($input, FILTER_VALIDATE_EMAIL)) {

            $emailParts = explode('@', $input);
            $username = $emailParts[0];
            $hiddenUsername = substr($username, 0, 2).str_repeat('*', strlen($username) - 2);
            $hiddenEmailDomain = substr($emailParts[1], 0, 2).str_repeat('*', strlen($emailParts[1]) - 3).$emailParts[1][strlen($emailParts[1]) - 1];

            return $hiddenUsername.'@'.$hiddenEmailDomain;

        }

        return preg_replace('/(\d{3})\d{3}(\d{3})/', '$1****$2', $input);

    }
}

function creditReferralBonus($user, $type, $mainAmount, $level = null, $depth = 1, $fromUser = null)
{
    $LevelReferral = \App\Models\LevelReferral::where('type', $type)->where('the_order', $depth)->first('bounty');
    if (null != $user->ref_id && $depth <= $level && $LevelReferral) {
        $referrer = \App\Models\User::find($user->ref_id);

        $bounty = $LevelReferral->bounty;
        $amount = (float) ($mainAmount * $bounty) / 100;

        $fromUserReferral = $fromUser == null ? $user : $fromUser;

        $description = ucwords($type).' Referral Bonus Via '.$fromUserReferral->full_name.' - Level '.$depth;

        Txn::new(
            $amount, 
            0, 
            $amount, 
            'system', 
            $description, 
            TxnType::Referral, 
            TxnStatus::Success, 
            null, 
            null, 
            $referrer->id, 
            $fromUserReferral->id, 
            'User', 
            [], 
            'none', 
            $depth, 
            $type, 
            true
        );

        // $referrer->profit_balance += $amount;
        $referrer->commission_balance += $amount;

        $referrer->save();
        creditReferralBonus($referrer, $type, $mainAmount, $level, $depth + 1, $user);
    }
}

if (! function_exists('txn_type')) {
    function txn_type($type, $value = [])
    {
        $result = [];
        switch ($type) {
            case TxnType::Interest->value:
            case TxnType::ReceiveMoney->value:
            case TxnType::Deposit->value:
            case TxnType::ManualDeposit->value:
            case TxnType::Bonus->value:
            case TxnType::Refund->value:
            case TxnType::Exchange->value:
            case TxnType::Referral->value:
            case TxnType::SendCommission->value:
            case TxnType::ProfitShare->value:
                $result = ['green-color', '+'];
                break;
            case TxnType::SendMoney->value:
            case TxnType::Investment->value:
            case TxnType::Withdraw->value:
            case TxnType::Subtract->value:
                $result = ['red-color', '-'];
                break;
        }
        $commonResult = array_intersect($value, $result);

        return current($commonResult);
    }
}

if (! function_exists('is_custom_rate')) {
    function is_custom_rate($gateway_code)
    {
        if (in_array($gateway_code,['nowpayments','coinremitter','blockchain'])) {
            return 'USD';
        }
        return null;
    }
}

if (! function_exists('site_theme')) {
    function site_theme()
    {
        $theme = new \App\Models\Theme();

        return $theme->active();
    }
}
if (! function_exists('generate_date_range_array')) {
    function generate_date_range_array($startDate, $endDate): array
    {
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);

        $dates = collect([]);

        while ($startDate->lte($endDate)) {
            $dates->push($startDate->format('d M'));
            $startDate->addDay();
        }

        return $dates->toArray();
    }
}
if (! function_exists('calPercentage')) {
    function calPercentage($num, $percentage) {
        return $num * ($percentage / 100);
    }
}
if (! function_exists('getQRCode')) {
    function getQRCode($data) {

        return "https://api.qrserver.com/v1/create-qr-code/?size=400x400&data=$data";
    };
}

