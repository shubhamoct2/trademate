@php
    $amount = json_decode($data)->amount;
@endphp
<strong class="{{ $amount > 0 ? 'green-color': 'red-color'}}">{{ ( $amount > 0 ? '+': '' ).$amount.' '.$currency }}</strong>
