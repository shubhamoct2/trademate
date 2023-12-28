@extends('frontend::layouts.user')
@section('title')
    {{ __('Withdraw') }}
@endsection
@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="site-card">
                <div class="site-card-header">
                    <h3 class="title">{{ __('Withdraw') }}</h3>
                    @if (!$locked)
                    <div class="card-header-links">
                    <a href="{{ route('user.withdraw.log') }}"
                           class="card-header-link">{{ __('Withdraw History') }}</a>
                    </div>
                    @endif
                </div>      
                <div class="site-card-body">
                    @if ($locked)                
                    <section class="disabled-section w-100">
                        <div class="container">
                            <div class="section-body">
                                <h2 class="title">
                                    {{ trans('translation.lock_feature', ['feature' => __('Withdraw') ]) }}
                                </h2>
                                <h4 class="description">
                                    {{ __('To Access This Feature, You Must Complete Your KYC & Request It To Be Unlocked & Complete Your KYC.') }}
                                </h4>
                                <div class="action">
                                    <form method="POST" action="{{ route('user.unlock') }}">
                                        @csrf
                                        <input type="hidden" id="feature" name="feature" value="{{ __('Withdraw') }}">
                                        <button type="submit" class="site-btn grad-btn">
                                            {{ __('COMPLETE KYC & UNLOCK') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </section>
                    @else
                    <div class="site-card-header">
                        <h3 class="title">{{ __('Withdraw Money') }}</h3>
                        @if (!is_null($wallet))
                        <div class="card-header-links">
                            <a href="{{ route('user.setting.show') }}"
                            class="card-header-link">{{ __('ADD WITHDRAWAL ACCOUNT') }}</a>
                        </div>
                        @endif
                    </div>
                    <div class="site-card-body">
                        @if ($wallet)
                        <div class="progress-steps-form">
                            <form id="withdraw_form" action="{{ route('user.withdraw.now') }}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-xl-6 col-md-12 mb-3 ">
                                        <div class="single-box">
                                            <label for="gatewaySelect"
                                                class="form-label">{{ __('Payment Method:') }}</label>
                                            <div class="input-group">
                                                <select name="gateway" id="gatewaySelect" class="site-nice-select" required>
                                                    <option value="">--{{ __('Select Gateway') }}--</option>
                                                    @foreach($gateways as $gateway)
                                                        <option value="{{ $gateway->gateway_code }}">{{ $gateway->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="input-info-text processing-time"></div>
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-md-12">
                                        <div class="single-box">
                                            <label for="amount" class="form-label">{{ __('Amount') }}</label>
                                            <div class="input-group">
                                                <input type="text" name="amount" id="amount"
                                                    oninput="this.value = validateDouble(this.value)"
                                                    class="form-control withdrawAmount" placeholder="Enter Amount" required>
                                            </div>
                                            <div class="input-info-text withdrawAmountRange"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="transaction-list table-responsive">
                                    <div class="user-panel-title">
                                        <h3>{{ __('Withdraw Details') }}</h3>
                                    </div>
                                    <table class="table">
                                        <tbody class="selectDetailsTbody">
                                            <tr class="detailsCol">
                                                <td><strong>{{ __('Balance') }}</strong></td>
                                                <td><span class=""></span>{{ $user->balance }}</td>
                                            </tr>
                                            <tr class="detailsCol">
                                                <td><strong>{{ __('Payment Method') }}</strong></td>
                                                <td id="logo"><img src="" class="payment-method" alt=""></td>
                                            </tr>
                                            <tr class="detailsCol">
                                                <td><strong>{{ __('Currency') }}</strong></td>
                                                <td>
                                                    <span class="">{{ $wallet->currency }}</span>
                                                </td>
                                            </tr>
                                            
                                            <tr class="detailsCol">
                                                <td><strong>{{ __('Address') }}</strong></td>
                                                <td><span class="">{{ $wallet->address }}</span></td>
                                            </tr>

                                            <tr class="detailsCol">
                                                <td><strong>{{ __('Amount') }}</strong></td>
                                                <td><span class="amount"></span></td>
                                            </tr>
                                            <tr>
                                                <td><strong>{{ __('Charge') }}</strong></td>
                                                <td class="charge"></td>
                                            </tr>
                                            <tr>
                                                <td><strong>{{ __('Total') }}</strong></td>
                                                <td class="total"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="buttons">
                                    <button type="submit" class="site-btn blue-btn">
                                        {{ __('Withdraw Money') }}<i class="anticon anticon-double-right"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        "use strict";

        var globalData = [];

        $("#gatewaySelect").on('change',function (e) {
            e.preventDefault();

            var code = $(this).val();
            if (code != undefined) {
                var url = '{{ route("user.withdraw.gateway",":code") }}';
                url = url.replace(':code', code);
                $.get(url, function (data) {
                    $('label.error').hide();

                    globalData = data;
                    $('#logo').html(`<img class="payment-method" src='${data.icon}'>`);
                    $('.withdrawAmountRange').text(data.range)
                })
            }
        })

        $('#amount').on('keyup', function (e) {
            "use strict"

            var amount = $(this).val();
            $('.amount').text((Number(amount)));
            var charge = globalData.charge_type === 'percentage' ? calPercentage(amount, globalData.charge) : globalData.charge;
            $('.charge').text(charge);
            var total = (Number(amount) + Number(charge)) * Number(globalData.price);
            $('.total').text(total);
        })

        $(document).ready(function() { 
            jQuery.validator.addMethod('selectcheck', function (value) {
                return (value != '');
            }, "Value required");
            
            $('#withdraw_form').validate({ 
                ignore: [],
                rules: {
                    gateway: {
                        selectcheck: true
                    },
                    amount: {
                        required: true
                    }
                },
                errorPlacement: function(error, element) {
                    error.insertAfter(element.parent());
                }
            });
        })        
        
    </script>
@endsection
