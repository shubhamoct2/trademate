<!DOCTYPE html>
<html lang="en">
@include('frontend::include.__head')

<body class="{{ session()->get('site-color-mode') ?? 'dark-theme' }}">
    @include('notify::components.notify')
    <!--Full Layout-->
    <div class="panel-layout">
        <!--Header-->
        @include('frontend::include.__user_header')
        <!--/Header-->

        <div class="desktop-screen-show">
            @include('frontend::include.__user_side_nav')
        </div>

        <div class="page-container">
            <div class="main-content">
                <div class="section-gap">
                    <div class="container-fluid">
                        @if(setting('kyc_verification','permission'))
                        {{-- Kyc Info--}}
                        @include('frontend::user.include.__kyc_info')
                        @include('frontend::user.mobile_screen_include.kyc.__user_kyc_mobile')
                        @endif
                        <!--Page Content-->
                        @yield('content')
                        <!--Page Content-->
                    </div>
                </div>
            </div>
        </div>


        <!-- Show in 575px in Mobile Screen -->
        <div class="mobile-screen-show">
            @include('frontend::user.mobile_screen_include.__menu')
        </div>

        <!-- Show in 575px in Mobile Screen End -->

        <!-- Automatic Popup -->
        @if(Session::get('signup_bonus'))
        @include('frontend::user.include.__signup_bonus')
        @endif
        <!-- /Automatic Popup End -->

        <!-- Modal for Join Telegram Channel //with {cookies}-->
        @if(!isset($_COOKIE['showJoinTelegramPreview']))
           @include('frontend::user.include.__modal_join_telegram')
          @php setcookie('showJoinTelegramPreview','yes',strtotime( '+1 days' )); @endphp
        @endif
        <!-- Modal for Join Telegram Channel -->
    </div>
    <!--/Full Layout-->

    @include('frontend::include.__script')

    <script>
        jQuery(document).ready(function() {
            jQuery("#joinTelegramModal").modal('show')
        })
    </script>

</body>

</html>