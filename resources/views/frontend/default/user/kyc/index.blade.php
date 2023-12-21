@extends('frontend::layouts.user')
@section('title')
    {{ __('KYC') }}
@endsection
@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="site-card">
                <div class="site-card-header">
                    <h3 class="title">{{ __('KYC') }}</h3>
                </div>
                <div class="site-card-body">
                    @if ($kycStatus == 'Pending')
                        <div class="site-badge warnning"> {{ __('Your Kyc Is Pending') }}</div>
                    @elseif ($kycStatus == 'Verified')
                        <div class="site-badge success"> {{ __('Your Kyc Is Verified') }} </div>
                    @else
                        <form action="{{ route('user.kyc.submit') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="step"  value="{{ $step }}" />
                            <div class="col-xl-12 col-md-12">
                                <div class="progress-steps-form">                                    
                                    <div class="row w-100">                                        
                                        <input type="hidden" name="direction"  value="{{ true }}" />
                                        @if ($step == 0)         
                                        <div class="col-12">
                                            <h2 class="step-title">{{ __('What best describes you?') }}</h2>
                                        </div>                               
                                        <div class="col-md-4 col-12">
                                            <div class="radio-item">
                                                <input type="radio" name="kyc_type" id="kyc_type_company" value="company" />
                                                <label for="kyc_type_company">
                                                    <i icon-name="globe"></i>
                                                    <h5>{{ __('Company') }}</h5>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-12">
                                            <div class="radio-item">
                                                <input type="radio" name="kyc_type" id="kyc_type_individual" value="individual" />
                                                <label for="kyc_type_individual">
                                                    <i icon-name="user"></i>
                                                    <h5>{{ __('Private Individual') }}</h5>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-12">
                                            <div class="radio-item">
                                                <input type="radio" name="kyc_type" id="kyc_type_ubo" value="ubo" />
                                                <label for="kyc_type_ubo">
                                                    <i icon-name="landmark"></i>
                                                    <h5>{{ __('Corporate UBO') }}</h5>
                                                </label>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="field-step-btn">
                                        @if ($step > 1)
                                        <button type="submit" class="site-btn blue-btn">{{ __('Back') }}</button>
                                        @endif
                                        @if ($step < $max_step)
                                        <button type="submit" class="site-btn blue-btn">{{ __('Next') }}</button>
                                        @elseif ($step == $max_step)
                                        <button type="submit" class="site-btn blue-btn">{{ __('Complete') }}</button>
                                        @endif
                                    </div>
                                </div>
                            </div>                            
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $("#kycTypeSelect").on('change',function (e) {
            "use strict"
            e.preventDefault();

            $('.kycData').empty();

            var id = $(this).val();

            var url = '{{ route("user.kyc.data",":id") }}';
            url = url.replace(':id', id);
            $.get(url, function (data) {

                console.log(data);
                $('.kycData').append(data)
                imagePreview()

            });


        });
    </script>
@endsection
