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
                        <div class="card-header-links">
                            <a href="{{ route('user.withdraw.account.index') }}"
                            class="card-header-link">{{ __('ADD WITHDRAWAL ACCOUNT') }}</a>
                        </div>
                    </div>
                    <div class="site-card-body">
                        <div class="progress-steps-form">
                            <form id="withdraw_form" action="{{ route('user.withdraw.now') }}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-xl-6 col-md-12 mb-3 ">
                                        <div class="single-box">
                                            <label for="withdrawAccountId"
                                                class="form-label">{{ __('Withdraw Account') }}</label>
                                            <div class="input-group">
                                                <select name="withdraw_account" id="withdrawAccountId" class="site-nice-select" required>
                                                    <option value="">{{ __('Choose...') }}</option>
                                                    @foreach($accounts as $account)
                                                    <option value="{{ $account->id }}">{{ $account->method_name }}</option>
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
                                            <td><strong>{{ __('Withdraw Amount') }}</strong></td>
                                            <td><span class="withdrawAmount"></span> {{$currency}}</td>
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
        var info = [];
        $("#withdrawAccountId").on('change',function (e) {
            e.preventDefault();

            $('.selectDetailsTbody').children().not(':first', ':second').remove();
            var accountId = $(this).val()
            var amount = $('.withdrawAmount').val();

            if (!isNaN(accountId)) {
                var url = '{{ route("user.withdraw.details",['accountId' => ':accountId', 'amount' => ':amount']) }}';
                url = url.replace(':accountId', accountId,);
                url = url.replace(':amount', amount);

                $.get(url, function (data) {
                    $(data.html).insertAfter(".detailsCol");
                    info = data.info;
                    $('.withdrawAmountRange').text(info.range)
                    $('.processing-time').text(info.processing_time)
                })
            }
        })

        $(".withdrawAmount").on('keyup',function (e) {
            "use strict"
            e.preventDefault();
            var amount = $(this).val()
            var charge = info.charge_type === 'percentage' ? calPercentage(amount, info.charge) : info.charge
            $('.withdrawAmount').text(amount)
            $('.withdrawFee').text(charge)
            $('.processing-time').text(info.processing_time)
            $('.withdrawAmountRange').text(info.range)
            $('.pay-amount').text(amount * info.rate +' '+ info.pay_currency)
        })

        $(document).ready(function() { 
            jQuery.validator.addMethod('selectcheck', function (value) {
                return (value != '');
            }, "Value required");
            
            $('#withdraw_form').validate({ 
                ignore: [],
                rules: {
                    withdraw_account: {
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
