<div class="row mobile-screen-show">
    <div class="col-12">
        <div class="user-kyc-mobile">
            @if($user->kycInfo && $user->kycInfo->status == \App\Enums\KycStatus::Pending)
                <i icon-name="fingerprint" class="kyc-star"></i>
                {{ __('KYC Pending') }}
            @elseif(is_null($user->kycInfo) || $user->kycInfo->status != \App\Enums\KycStatus::Verified)
                <i icon-name="fingerprint" class="kyc-star"></i>
                {{ __('Please Verify Your Identity') }} <a
                    href="{{ route('user.kyc') }}">{{ __('Submit Now') }}</a>
            @endif
        </div>
    </div>
</div>
