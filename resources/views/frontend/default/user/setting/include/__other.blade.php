<div class="row">

    {{-- 2 Factor Authentication --}}
    @include('frontend::user.setting.include.__two_fa')

    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
        @if(setting('kyc_verification','permission'))
            <div class="site-card">
                <div class="site-card-header">
                    <h3 class="title">{{ __('KYC') }}</h3>
                </div>
                <div class="site-card-body">

                    @if($user->kyc == \App\Enums\KYCStatus::Verified->value)
                        <div class="site-badge success">{{ __('KYC Verified') }}</div>
                        <p class="mt-3">{{ json_decode($user->kyc_credential,true)['Action Message'] ?? '' }}</p>
                    @else
                        <a href="{{ route('user.kyc') }}" class="site-btn blue-btn">{{ __('Upload KYC') }}</a>
                        <p class="mt-3">{{ json_decode($user?->kyc_credential,true)['Action Message'] ?? '' }}</p>
                    @endif
                </div>
            </div>
        @endif

        <div class="site-card">
            <div class="site-card-header">
                <h3 class="title">{{ __('Withdrawal Address') }}</h3>
            </div>
            <div class="site-card-body">
                @php
                    $address = json_decode($user->withdrawal_address);
                @endphp
                <form action="{{ route('user.setting.withdrawal-update') }}" method="post" name="withdraw_update" id="withdraw_update" class="progress-steps-form">
                    @csrf
                    <div class="row">
                        <label for="withdrwal_currency" class="form-label">{{ __('Currency') }}</label>
                        <div class="input-group">
                            <select name="currency" id="withdrwal_currency" class="nice-select site-nice-select" required>
                                <option value="">{{ __('Choose...') }}</option>
                                <option value="btc" @if($address && $address->currency == "btc") selected @endif>BTC</option>
                                <option value="eth" @if($address && $address->currency == "eth") selected @endif>ETH</option>
                                <option value="usdt" @if($address && $address->currency == "usdt") selected @endif>USDT</option>
                            </select>
                        </div>
                    </div>
                    <div id="select_blockchain" class="row site-input-groups my-3 @if(!isset($address->blockchain)) hidden @endif">
                        <label class="box-input-label" for="">{{ __('Blockchain') }}</label>
                        <div class="switch-field same-type">
                            <input
                                type="radio"
                                id="radio-five"
                                name="blockchain"
                                value="eth"
                                @if($address && isset($address->blockchain) && $address->blockchain == "eth") checked 
                                @elseif(!isset($address->blockchain)) checked
                                @endif
                            />
                            <label for="radio-five">{{ 'ETH' }}</label>
                            <input
                                type="radio"
                                id="radio-six"
                                name="blockchain"
                                value="bsc"
                                @if($address && isset($address->blockchain) && $address->blockchain == "bsc") checked @endif
                            />
                            <label for="radio-six">{{ 'BSC' }}</label>
                        </div>
                    </div>
                    <div class="row">
                        <label for="withdrawal_address" class="form-label">{{ __('Address') }}</label>
                        <div class="input-group">
                            <input
                                name="address"
                                id="withdrawal_address"
                                type="text"
                                class="form-control"
                                value="@if($address) $address->address @endif"
                                placeholder="Address"
                                required
                            />
                        </div>
                    </div>
                    <div class="buttons mt-2">
                        <button type="submit" class="site-btn blue-btn">{{ __('Update') }}</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="site-card">
            <div class="site-card-header">
                <h3 class="title">{{ __('Change Password') }}</h3>
            </div>
            <div class="site-card-body">
                <a href="{{ route('user.change.password') }}" class="site-btn blue-btn">{{ __('Change Password') }}</a>
            </div>
        </div>
    </div>
</div>
@section('script')
    <script>
        $(document).ready(function() { 
            jQuery.validator.addMethod('selectcheck', function (value) {
                return (value != '');
            }, "Value required");
            
            $('#withdraw_update').validate({ 
                ignore: [],
                rules: {
                    currency: {
                        selectcheck: true
                    },
                    address: {
                        required: true
                    }
                },
                errorPlacement: function(error, element) {
                    error.insertAfter(element.parent());
                }
            });

            $('#withdrwal_currency').on('change', function () {
                var selectVal = $("#withdrwal_currency option:selected").val();

                if (selectVal == "usdt") {
                    $('#select_blockchain').show();
                } else {
                    $('#select_blockchain').hide();
                }
            });
        })        
        
    </script>
@endsection