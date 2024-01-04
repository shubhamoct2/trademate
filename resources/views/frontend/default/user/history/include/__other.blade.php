<div class="row">

    {{-- 2 Factor Authentication --}}
    @include('frontend::user.setting.include.__two_fa')

    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
        @if(setting('kyc_verification','permission'))
            <div class="site-card">
                <div class="site-card-header">
                    <h3 class="title">{{ __('KYC') }}</h3>
                    @if ($user->kycInfo)
                    <div class="">
                        @switch($user->kycInfo->status)
                            @case(\App\Enums\KycStatus::Verified)
                                <div class="site-badge success">{{ __('Verified') }}</div>
                                @break
                            @case(\App\Enums\KycStatus::Pending)
                                <div class="site-badge pending">{{ __('Pending') }}</div>
                                @break
                            @case(\App\Enums\KycStatus::Failed)
                                <div class="site-badge danger">{{ __('Rejected') }}</div>
                                @break
                            @case(\App\Enums\KycStatus::Draft)
                                <div class="site-badge pending">{{ __('Draft') }}</div>
                                @break
                        @endswitch
                    </div>
                    @endif
                </div>
                <div class="site-card-body">
                    @if($user->kycInfo && $user->kycInfo->status == \App\Enums\KycStatus::Verified)
                        <div class="site-badge success">{{ __('KYC Verified') }}</div>
                        <p class="mt-3">{{ isset($user->kycInfo->data['action_message']) ? $user->kycInfo->data['action_message'] : '' }}</p>
                    @else
                        <a href="{{ route('user.kyc') }}" class="site-btn blue-btn">{{ __('Upload KYC') }}</a>
                        @if ($user->kycInfo && $user->kycInfo->status == \App\Enums\KycStatus::Failed)
                        <p class="mt-3">{{ isset($user->kycInfo->data['action_message']) ? $user->kycInfo->data['action_message'] : '' }}</p>
                        @endif
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
                    $wallet = $user->wallet();
                @endphp
                <form 
                    action="{{ route('user.setting.withdrawal-update') }}" 
                    method="post" 
                    name="withdraw_update" 
                    id="withdraw_update" 
                    class="progress-steps-form wallet-update">
                    @csrf
                    <div class="row">
                        <label for="wallet_name" class="form-label">{{ __('Name') }}</label>
                        <div class="input-group">
                            <input
                                name="name"
                                id="wallet_name"
                                type="text"
                                class="form-control"
                                value="{{ $wallet ? $wallet->name : '' }}"
                                placeholder="Wallet Name"
                                @if (!$user->two_fa) disabled @endif
                            />
                        </div>
                    </div>
                    <div class="row">
                        <label for="withdrwal_currency" class="form-label">{{ __('Currency') }}</label>
                        <div class="input-group">
                            <select name="currency" id="withdrwal_currency" class="nice-select site-nice-select" @if (!$user->two_fa) disabled @endif>
                                <option value="">{{ __('Choose...') }}</option>
                                <option value="BTC" 
                                    @if ($wallet && $wallet->currency == "BTC") selected @endif
                                >BTC</option>
                                <option value="ETH" 
                                    @if ($wallet && $wallet->currency == "ETH") selected @endif
                                >ETH</option>
                                <option value="USDT" 
                                    @if ($wallet && ($wallet->currency == "USDTE" || $wallet->currency == "USDTT")) selected @endif
                                >USDT</option>
                            </select>
                        </div>
                    </div>
                    <div id="select_blockchain" class="row site-input-groups my-3 
                        @if(is_null($wallet) || ($wallet->currency == 'BTC' || $wallet->currency == 'ETH')) hidden @endif">
                        <label class="box-input-label" for="">{{ __('Blockchain') }}</label>
                        <div class="switch-field same-type">
                            <input
                                type="radio"
                                id="radio-five"
                                name="blockchain"
                                value="erc20"
                                @if ($wallet && $wallet->currency == "USDTE") checked
                                @elseif (is_null($wallet)) checked
                                @endif
                                @if (!$user->two_fa) disabled @endif
                            />
                            <label for="radio-five">{{ 'ERC20' }}</label>
                            <input
                                type="radio"
                                id="radio-six"
                                name="blockchain"
                                value="trc20"
                                @if ($wallet && $wallet->currency == "USDTT") checked 
                                @endif
                                @if (!$user->two_fa) disabled @endif
                            />
                            <label for="radio-six">{{ 'TRC20' }}</label>
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
                                value="{{ $wallet ? $wallet->address : '' }}"
                                placeholder="Address"
                                @if (!$user->two_fa) disabled @endif
                            />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                name="disclaimer_check"
                                id="disclaimer_check"
                                @if (!$user->two_fa) disabled @endif
                            />
                            <label class="form-check-label" for="disclaimer_check">
                            {{ __('Please check double if you put in the right Wallet Address with the right Blockchain to send your funds to. Please be aware that we cannot take any responsibility for Tokens send to the wrong blockchain or wallet.These Coins or Tokens could be lost forever, and we do not reimburse or cover these loses.') }}
                            </label>
                        </div>
                    </div>
                    <div class="buttons mt-2">
                        @if ($user->two_fa)
                        <button type="submit" class="site-btn blue-btn">{{ __('Update') }}</button>
                        @else
                        <div class="warning-rounded">{{ __('2FA must be enabled for wallet update') }}</button>
                        @endif
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

            jQuery.validator.addMethod('checkbox_checked', function (value, element) {
                if (!element.checked) {
                    element.focus();
                }

                return element.checked;
            }, "");
            
            $('#withdraw_update').validate({ 
                ignore: [],
                rules: {
                    currency: {
                    },
                    address: {
                        required: true
                    },
                    name: {
                        required: true
                    },
                    disclaimer_check: {
                        checkbox_checked: true,
                    }
                },
                errorPlacement: function(error, element) {
                    error.insertAfter(element.parent());
                }
            });

            $('#withdrwal_currency').on('change', function () {
                var selectVal = $("#withdrwal_currency option:selected").val();

                if (selectVal == "USDT") {
                    $('#select_blockchain').show();
                } else {
                    $('#select_blockchain').hide();
                }
            });
        })        
        
    </script>
@endsection