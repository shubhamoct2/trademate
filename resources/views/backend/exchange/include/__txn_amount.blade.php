@if ($method == 'Alphapo')
    @if ($txID)
    <strong class="{{ $type !== 'subtract' && $type !== 'investment' && $type !==  'withdraw' && $type !==  'send_money' ? 'green-color': 'red-color'}}">{{ ($type !== 'subtract' && $type !== 'investment' && $type !==  'withdraw' && $type !==  'send_money' ? '+': '-' ).$final_amount.' '.$pay_currency }}</strong>
    @else
    <strong class="{{ $type !== 'subtract' && $type !== 'investment' && $type !==  'withdraw' && $type !==  'send_money' ? 'green-color': 'red-color'}}">{{ ($type !== 'subtract' && $type !== 'investment' && $type !==  'withdraw' && $type !==  'send_money' ? '+': '-' ).$amount.' '.$pay_currency }}</strong>
    @endif
@else
<strong class="{{ $type !== 'subtract' && $type !== 'investment' && $type !==  'withdraw' && $type !==  'send_money' ? 'green-color': 'red-color'}}">{{ ($type !== 'subtract' && $type !== 'investment' && $type !==  'withdraw' && $type !==  'send_money' ? '+': '-' ).$amount.' '.$currency }}</strong>
@endif
