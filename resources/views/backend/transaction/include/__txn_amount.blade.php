@if ($type == 'send_commission')
    <strong class="{{ $amount > 0 ? 'green-color': 'red-color'}}">{{ ($amount > 0 ? '+': '' ).$amount.' '.$currency }}</strong>
@else
    <strong class="{{ txn_type($type, ['green-color','red-color']) }}">{{ txn_type($type, ['+', '-']) . $amount . ' ' . ($pay_currency ? $pay_currency : $currency) }}</strong>
@endif  