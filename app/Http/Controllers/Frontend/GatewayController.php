<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\GatewayType;
use App\Http\Controllers\Controller;
use App\Models\DepositMethod;
use App\Models\WithdrawMethod;
use App\Traits\NotifyTrait;
use App\Traits\Payment;
use Auth;
use App\Libraries\AlphaPo;
use Illuminate\Support\Facades\Log;

class GatewayController extends Controller
{
    use NotifyTrait, Payment;

    public function gateway($code)
    {
        $gateway = DepositMethod::code($code)->first();

        if ($gateway->gateway_code == 'alphapo') {
            if (config('app.env') === 'production') {
                $alphapoSetting = config('alphapo.prod');
            } else {
                $alphapoSetting = config('alphapo.sandbox');
            }

            $gateway = array_merge($gateway->toArray(), ['credentials' => view('frontend::gateway.include.alphapo', compact('alphapoSetting'))->render()]);
        } else {
            if ($gateway->type == GatewayType::Manual->value) {
                $fieldOptions = $gateway->field_options;
                $paymentDetails = $gateway->payment_details;
                $gateway = array_merge($gateway->toArray(), ['credentials' => view('frontend::gateway.include.manual', compact('fieldOptions', 'paymentDetails'))->render()]);
            } else {
                $gatewayCurrency =  is_custom_rate($gateway->gateway->gateway_code) ?? $gateway->currency;
                $gateway['currency'] = $gatewayCurrency;
            }
        }
        return $gateway;
    }

    public function withdrawalGateway($code)
    {
        $gateway = WithdrawMethod::where('gateway_code', $code)->first();

        $address = json_decode(Auth::user()->withdrawal_address);

        if ($gateway->gateway_code == 'alphapo') {
            if (config('app.env') === 'production') {
                $alphapoSetting = config('alphapo.prod');
            } else {
                $alphapoSetting = config('alphapo.sandbox');
            }

            $currencySetting = null;
            foreach($alphapoSetting['currencies'] as $currency) {
                if ($currency['currency'] == strtoupper($address->currency)) {
                    $currencySetting = $currency;
                    break;
                }
            }

            $gateway = $gateway->toArray();
            $gateway['icon'] = env('ASSET_URL') . '/assets/' . $gateway['icon'];
            $gateway['range'] = trans('translation.gateway_payment_range', [
                'min' => $currencySetting['minimum_withdraw_amount'],
                'max' => __('Unlimited'),
            ]);

            $alphaPo = new AlphaPo;
            $gateway['price'] = $alphaPo->getCryptoPrice($currencySetting['currency']);
        } 
        
        return $gateway;
    }

    //list json
    public function gatewayList()
    {
        $gateways = DepositMethod::where('status', 1)->get();

        return view('frontend::gateway.include.__list', compact('gateways'));
    }
}
