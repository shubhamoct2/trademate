@extends('backend.layouts.app')
@section('title')
    {{ __('Profit Wallet Admin') }}
@endsection
@section('content')
    <div class="main-content">
        <div class="page-title">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <div class="title-content">
                            <h2 class="title">{{ __('Profit Wallet Admin') }} </h2>
                            <a href="{{ url()->previous() }}" class="title-btn"><i
                                    icon-name="corner-down-left"></i>{{ __('Back') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid">
                <div class="site-card">
                    <div class="site-card-header">
                        <h4 class="title">{{ __('Daily Profit Distribution') }}</h4>
                    </div>
                    <div class="site-card-body">
                        <form action="{{ route('admin.profit.push') }}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 mx-auto">
                                    <div class="site-input-groups">
                                        <label for="send_profit" class="box-input-label">{{ __('Please Input Daily Profit') }}</label>
                                        <input id="send_profit" name="profit" type="text" class="box-input" value="" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 mx-auto">
                                    <button type="submit" class="site-btn-sm primary-btn w-100 centered mb-4">{{ __('Send') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
