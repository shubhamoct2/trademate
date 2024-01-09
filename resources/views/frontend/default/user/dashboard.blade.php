@extends('frontend::layouts.user')
@section('title')
    {{ __('Dashboard') }}
@endsection
@section('content')

        <div class="desktop-screen-show">
            {{--Referral and Ranking --}}
            @include('frontend::user.include.__referral_ranking')

            {{-- User Card--}}
            @include('frontend::user.include.__user_card')

            {{--Recent Transactions--}}
            @include('frontend::user.include.__recent_transaction')
        </div>

         {{--for mobile--}}
        <div class="mobile-screen-show">
            @include('frontend::user.mobile_screen_include.dashboard.__index')
        </div>


@endsection

@section('script')
    <script>
        function copyRef() {
            var dummy = document.createElement("textarea");
            dummy.value = document.getElementById("refLink").value;
            document.body.appendChild(dummy);
            dummy.select();
            document.execCommand("copy");
            document.body.removeChild(dummy);

            $('#copy').text($('#copied').val());
        }

        // Load More
        $('.moreless-button').click(function () {
            $('.moretext').slideToggle();
            if ($('.moreless-button').text() == "Load more") {
                $(this).text("Load less")
            } else {
                $(this).text("Load more")
            }
        });

        $('.moreless-button-2').click(function () {
            $('.moretext-2').slideToggle();
            if ($('.moreless-button-2').text() == "Load more") {
                $(this).text("Load less")
            } else {
                $(this).text("Load more")
            }
        });
    
    </script>
@endsection