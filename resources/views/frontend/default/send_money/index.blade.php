@extends('frontend::layouts.user')
@section('title')
    {{ __('Send To') }}
@endsection
@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="site-card">
                <div class="site-card-header">
                    <h3 class="title">{{ __('Send To') }}</h3>
                    @if (!$locked)
                    <div class="card-header-links">
                        <a href="{{ route('user.send-money.log') }}"
                           class="card-header-link">{{ __('SEND TO LOG') }}</a>
                    </div>
                    @endif
                </div>
                <div class="site-card-body">
                    @if (!$locked)
                    <div class="progress-steps">
                        <div class="single-step {{ $isStepOne }}">
                            <div class="number">{{ __('01') }}</div>
                            <div class="content">
                                <h4>{{ __('Payment Details') }}</h4>
                                <p>{{ __('Enter your Payment details') }}</p>
                            </div>
                        </div>
                        <div class="single-step {{ $isStepTwo }}">
                            <div class="number">{{ __('02') }}</div>
                            <div class="content">
                                <h4>{{ __('Success') }}</h4>
                                <p>{{  $notify['card-header'] ??  __('Successfully Payment') }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
                    @yield('send_money_content')
                </div>
            </div>
        </div>
    </div>
@endsection
