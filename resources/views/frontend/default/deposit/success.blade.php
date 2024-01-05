@extends('frontend::deposit.index')
@section('deposit_content')
    <div class="progress-steps-form">
        <div class="transaction-status centered">
            <div class="icon success">
                <i class="anticon anticon-check"></i>
            </div>
            <h2>{{ $notify['title'] }}</h2>
            <p>{{ $notify['p'] }}</p>
            <p>{{ $notify['strong'] }}</p>
            <p>{{ 'Please make a deposit using the following ' . $notify['data']['currency'] . ' address' }}</p>
            <div class="mb-3">
                <span id="wallet_address">{{ $notify['data']['address'] }}</span>
                <button type="button" class="wallet-copy-btn ml-2" onclick="copyRef()">
                    <i class="anticon anticon-copy"></i>
                    <span id="copy">{{ __('Copy') }}</span>
                </button>
            </div>
            <div style="margin: 20px 0 40px 0;">
            @if ($notify['data']['currency_name'] != 'usdt')            
                <a href="{{ $notify['data']['currency_name'] }}:{{ $notify['data']['address'] }}?amount={{ $notify['data']['amount'] }}" target="_blank">
                    <img src="https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl={{ $notify['data']['currency_name'] }}:{{ $notify['data']['address'] }}?amount={{ $notify['data']['amount'] }}&choe=UTF-8">
                </a>            
            @else
                <span>({{ $notify['data']['currency'] == 'USDTE' ? 'ERC-20' : 'TRC-20'}})</span>
            @endif
            </div>
            <a href="{{ $notify['action'] }}" class="site-btn">
                <i class="anticon anticon-plus"></i>{{ $notify['a'] }}
            </a>
        </div>
    </div>
@endsection
@section('script')
    <script>
        function copyRef() {
            var dummy = document.createElement("textarea");
            dummy.value = document.getElementById("wallet_address").textContent;
            document.body.appendChild(dummy);
            dummy.select();
            document.execCommand("copy");
            document.body.removeChild(dummy);

            $('#copy').text($('#copied').val());
        }
    </script>
@endsection

