@if(is_null($user->kycInfo) || ($user->kycInfo->status != \App\Enums\KyCStatus::Verified))
    <div class="row desktop-screen-show">
        <div class="col">
            <div class="alert site-alert alert-dismissible fade show" role="alert">
                <div class="content">
                    <div class="icon"><i class="anticon anticon-warning"></i></div>
                    @if($user->kycInfo && $user->kycInfo->status == \App\Enums\KyCStatus::Pending)
                        <strong>{{ __('KYC Pending') }}</strong>
                    @else
                        {{ __('Please complete') }}
                        @if(Route::is('user.wallet-exchange') )
                            <strong>{{ __('KYC') }}</strong> {{ __('in order to withdraw any profits.') }}
                        @else
                            <strong>{{ __('KYC') }}</strong> {{ __('to use all of TradeMates services.') }}
                        @endif                        
                    @endif
                </div>
                @if(is_null($user->kycInfo) || $user->kycInfo->status != \App\Enums\KYCStatus::Pending)
                    <div class="action">
                        <a href="{{ route('user.kyc') }}" class="site-btn-sm grad-btn"><i
                                class="anticon anticon-info-circle"></i>{{ __('Submit Now') }}</a>
                        <a href="" class="site-btn-sm grad-btn ms-2" type="button" data-bs-dismiss="alert"
                           aria-label="Close"><i class="anticon anticon-close"></i>{{ __('Later') }}</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endif
