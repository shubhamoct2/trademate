@extends('frontend::deposit.index')
@section('deposit_content')
    <div class="progress-steps-form">
        <div class="transaction-status centered">
            <div class="icon success">
                <i class="anticon anticon-check"></i>
            </div>
            <h2>{{ $notify['title'] }}</h2>
            <p>{{ $notify['p']}}</p>
            <p>{{ $notify['strong'] }}</p>
            <p>{{ 'Please make a deposit using the following ' . $notify['data']['currency'] . ' address' }}</p>
            <p>{{ $notify['data']['address'] }}</p>
            <!-- <div style="margin: 20px 0;">
                <a href="{{ $notify['data']['currency'] }}:{{ $notify['data']['address'] }}" target="_blank">
                    <img src="https://chart.googleapis.com/chart?chs=150x150&cht=qr&
                    chl={{ $notify['data']['currency'] }}:{{ $notify['data']['address'] }}&choe=UTF-8">
                </a>
            </div> -->
            <a href="{{ $notify['action'] }}" class="site-btn">
                <i class="anticon anticon-plus"></i>{{ $notify['a'] }}
            </a>
        </div>
    </div>
@endsection

