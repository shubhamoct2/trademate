<div class="row">
    <div class="col-12">
        <div class="user-ranking-mobile">
            <div class="icon"><img src="{{ asset($user->avatar ?? 'global/materials/user.png') }}" alt=""/></div>
            <div class="name">
                <h4>{{ __('Hi') }}, {{ $user->full_name }}</h4>
                <p>{{ $user->rank->ranking_name }} - <span>{{ $user->rank->ranking }}</span></p>
            </div>
            <div class="rank-badge"><img src="{{ asset( $user->rank->icon) }}" alt=""/></div>
        </div>
        <div class="user-wallets-mobile">
            <img src="{{ asset('frontend/materials/wallet-shadow.png') }}" alt="" class="wallet-shadow">
            <div class="head">{{ __('All Wallets in') }} {{ $currency }}</div>
            <div class="one">
                <div class="balance">
                    <span class="symbol">{{ $currencySymbol }}</span>{{ Str::before($user->balance, '.') }}<span
                        class="after-dot">.{{ strpos($user->balance, '.') ? Str::after($user->balance, '.') : '00' }} </span>
                </div>
                <div class="wallet">{{ __('Main Wallet') }}</div>
            </div>
            <div class="one p-wal">
                <div class="balance">
                    <span class="symbol">{{ $currencySymbol }}</span>{{ $user->profit_balance }}<span
                        class="after-dot">.{{ strpos($user->profit_balance, '.') ? Str::after($user->profit_balance, '.') : '00' }} </span>
                </div>
                <div class="wallet">{{ __('Profit Wallet') }}</div>
            </div>
            <div class="one p-wal">
                <div class="balance">
                    <span class="symbol">{{ $currencySymbol }}</span>{{ $user->trading_balance }}<span
                        class="after-dot">.{{ strpos($user->trading_balance, '.') ? Str::after($user->trading_balance, '.') : '00' }} </span>
                </div>
                <div class="wallet">{{ __('Trading Wallet') }}</div>
            </div>
            <div class="one p-wal">
                <div class="balance">
                    <span class="symbol">{{ $currencySymbol }}</span>{{ $user->commission_balance }}<span
                        class="after-dot">.{{ strpos($user->commission_balance, '.') ? Str::after($user->commission_balance, '.') : '00' }} </span>
                </div>
                <div class="wallet">{{ __('Commission Wallet') }}</div>
            </div>
            <!-- <div class="info">
                <i icon-name="info"></i>{{ __('You Earned') }} {{ $dataCount['profit_last_7_days'] }} {{ $currency }} {{ __('This Week') }}
            </div> -->
        </div>
    </div>

    <div class="col-12">
        <div class="mob-shortcut-btn">
            <a href="{{ route('user.deposit.amount') }}"><i icon-name="download"></i> {{ __('Deposit') }}</a>
            <a href="{{ route('user.withdraw.view') }}"><i icon-name="receipt"></i> {{ __('Withdraw') }}</a>
            <a href="{{ route('user.wallet-exchange') }}"><i icon-name="send"></i> {{ __('Internal Transfer') }}</a>
            
        </div>
    </div>


    <div class="col-12">
        <!-- all navigation -->
        @include('frontend::user.mobile_screen_include.dashboard.__navigations')

        <!-- all Statistic -->
        @include('frontend::user.mobile_screen_include.dashboard.__statistic')

        <!-- Recent Transactions -->
        @include('frontend::user.mobile_screen_include.dashboard.__transactions')
    </div>

    <div class="col-12">
        <div class="mobile-ref-url mb-4">
            <div class="all-feature-mobile">
                <div class="title">{{ __('Referral URL') }}</div>
                <div class="mobile-referral-link-form">
                    <input type="text" value="{{ $referral->link }}" id="refLink"/>
                    <button type="button" onclick="copyRef()">
                        <span id="copy">{{ __('Copy') }}</span>
                        <input id="copied" hidden value="{{ __('Copied') }}">
                    </button>
                </div>
                <p class="referral-joined">
                    @if ($referral->relationships()->count() == 1)
                        {{ $referral->relationships()->count() }} {{ __('person has joined through this url') }}
                    @else
                        {{ $referral->relationships()->count() }} {{ __('people have joined through this url') }}
                    @endif
                </p>
            </div>
        </div>
    </div>
</div>
