@extends('frontend::deposit.index')

@section('deposit_content')
    @if ($locked)
        <section class="disabled-section w-100">
            <div class="container">
                <div class="section-body">
                    <h2 class="title">
                        {{ trans('translation.lock_feature', ['feature' => __('Deposit') ]) }}
                    </h2>
                    <h4 class="description">
                        {{ __('To Access This Feature, You Must Complete Your KYC & Request It To Be Unlocked & Complete Your KYC.') }}
                    </h4>
                    <div class="action">
                        <form method="POST" action="{{ route('user.unlock') }}">
                            @csrf
                            <input type="hidden" id="feature" name="feature" value="{{ __('Deposit') }}">
                            <button type="submit" class="site-btn grad-btn">
                                {{ __('COMPLETE KYC & UNLOCK') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    @else
        <div class="progress-steps-form">
            <form action="{{ route('user.deposit.now') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-xl-6 col-md-12 mb-3">
                        <label for="exampleFormControlInput1" class="form-label">{{ __('Payment Method:') }}</label>
                        <div class="input-group">
                            <select name="gateway_code" id="gatewaySelect" class="site-nice-select">
                                <option selected disabled>--{{ __('Select Gateway') }}--</option>
                                @foreach($gateways as $gateway)
                                    <option value="{{ $gateway->gateway_code }}">{{ $gateway->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="input-info-text charge"></div>
                    </div>
                    <div class="col-xl-6 col-md-12">
                        <label for="exampleFormControlInput1" class="form-label">{{ __('Enter Amount:') }}</label>
                        <div class="input-group">
                            <input type="text" name="amount" class="form-control"
                                oninput="this.value = validateDouble(this.value)" aria-label="Amount" id="amount"
                                aria-describedby="basic-addon1">
                            <span class="input-group-text" id="basic-addon1">{{ $currency }}</span>
                        </div>
                        <div class="input-info-text min-max"></div>
                    </div>

                </div>

                <div class="row manual-row">
                </div>

                <div class="transaction-list table-responsive">
                    <div class="user-panel-title">
                        <h3>{{ __('Review Details:') }}</h3>
                    </div>
                    <table class="table">
                        <tbody>
                        <tr>
                            <td><strong>{{ __('Amount') }}</strong></td>
                            <td><span class="amount"></span> <span class="currency"></span></td>
                        </tr>
                        <tr>
                            <td><strong>{{ __('Charge') }}</strong></td>
                            <td class="charge2"></td>
                        </tr>
                        <tr>
                            <td><strong>{{ __('Payment Method') }}</strong></td>
                            <td id="logo"><img src="" class="payment-method" alt=""></td>
                        </tr>
                        <tr>
                            <td><strong>{{ __('Total') }}</strong></td>
                            <td class="total"></td>
                        </tr>
                        <tr class="conversion">
                            <td><strong>{{ __('Conversion Rate') }}</strong></td>
                            <td class="conversion-rate"></td>
                        </tr>
                        <tr class="conversion">
                            <td><strong>{{ __('Pay Amount') }}</strong></td>
                            <td class="pay-amount"></td>
                        </tr>


                        </tbody>
                    </table>
                </div>
                <div class="buttons">
                    <button type="submit" class="site-btn blue-btn">
                        {{ __('Proceed to Payment') }}<i class="anticon anticon-double-right"></i>
                    </button>
                </div>
            </form>
        </div>
    @endif
@endsection
@section('script')
    <script>

        var globalData;
        var currency = @json($currency);
        var default_currency = @json($currency);

        $("#gatewaySelect").on('change', function (e) {
            "use strict"
            e.preventDefault();
            $('.manual-row').empty();
            var code = $(this).val()
            var url = '{{ route("user.deposit.gateway",":code") }}';
            url = url.replace(':code', code);
            $.get(url, function (data) {

                globalData = data;
                currency = globalData.currency;

                if (data.currency === currency){
                    $('.conversion').addClass('hidden');
                }else {
                    $('.conversion').removeClass('hidden');
                }

                if (data.name === 'Crypto') {
                    currency = '';
                    $('.crypto_currency').removeClass('hidden');
                } else {
                    currency = default_currency
                    $('.crypto_currency').addClass('hidden');
                    $('.min-max').text('Minimum ' + data.minimum_deposit + ' ' + currency + ' and ' + 'Maximum ' + data.maximum_deposit + ' ' + currency)
                    $('#basic-addon1').text('USD');
                }

                $('.charge').text('Charge ' + data.charge + ' ' + (data.charge_type === 'percentage' ? ' % ' : currency))
                $('.conversion-rate').text('1' +' '+ currency + ' = ' + data.rate +' '+ data.currency)


                $('#logo').html(`<img class="payment-method" src='${data.gateway_logo}'>`);
                var amount = $('#amount').val()

                if (Number(amount) > 0) {
                    $('.amount').text((Number(amount)))
                    var charge = data.charge_type === 'percentage' ? calPercentage(amount, data.charge) : data.charge
                    $('.charge2').text(charge + ' ' + currency)
                    $('.total').text((Number(amount) + Number(charge)) + ' ' + currency)
                }

                if (data.credentials !== undefined) {
                    console.log(data.credentials);
                    
                    $('.manual-row').append(data.credentials)
                    imagePreview()

                    if ($("#crypto_currency_select")) {                        
                        $("#crypto_currency_select").on('change', function (e) {
                            e.preventDefault();

                            var code = $(this).val();
                            $('#basic-addon1').text(code);
                            globalData.currency = code;
                            currency = code;

                            var url = '{{ route("user.deposit.min",":code") }}';
                            url = url.replace(':code', code);
                            $.get(url, function (data) {
                                $('.min-max').text('Minimum ' + data + ' ' + currency);
                            });
                        });
                    }
                }

            });

            $('#amount').on('keyup', function (e) {
                "use strict"

                var amount = $(this).val()
                $('.amount').text((Number(amount)))

                var charge = globalData.charge_type === 'percentage' ? calPercentage(amount, globalData.charge) : globalData.charge
                $('.charge2').text(charge)

                var total = (Number(amount) + Number(charge));

                $('.total').text(total)

                $('.pay-amount').text(total * globalData.rate)
            })

            
        });

        $(".crypto_currency_select").on('change', function (e) {
            console.log(e.target.value);

            e.preventDefault();

            $('.crypto_currency').text(e.target.value);
        });
    </script>
@endsection
