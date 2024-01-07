<div class="all-feature-mobile mb-3 mobile-screen-show">
    <div class="title">{{ __('All Statistic') }}</div>
    <div class="row">
        <div class="col-12">
            <div class="all-cards-mobile">
                <div class="contents row">
                    <div class="col-12">
                        <div class="single-card">
                            <div class="icon"><i icon-name="download"></i></div>
                            <div class="content">
                                <div class="amount">{{ $currencySymbol }}<span
                                        class="count">{{ $dataCount['total_deposit'] }}</span>
                                </div>
                                <div class="name">{{ __('Total Deposit') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="single-card">
                            <div class="icon"><i icon-name="credit-card"></i></div>
                            <div class="content">
                                <div class="amount"> {{ $currencySymbol }}<span
                                        class="count">{{ $dataCount['total_profit'] }}</span>
                                </div>
                                <div class="name">{{ __('Total Profit') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="single-card">
                            <div class="icon"><i icon-name="send"></i></div>
                            <div class="content">
                                <div class="amount"> {{ $currencySymbol }}<span
                                        class="count">{{ $dataCount['total_withdraw'] }}</span>
                                </div>
                                <div class="name">{{ __('Total Withdraw') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="moretext-2">
                    <div class="contents row">
                        <div class="col-12">
                            <div class="single-card">
                                <div class="icon"><i icon-name="gift"></i></div>
                                <div class="content">
                                    <div class="amount count">{{ $dataCount['total_referral'] }}</div>
                                    <div class="name"> {{ __('Total Referral') }}</div>
                                </div>
                            </div>
                        </div>                        
                        <div class="col-12">
                            <div class="single-card">
                                <div class="icon"><i icon-name="log-in"></i></div>
                                <div class="content">
                                    <div class="amount">{{ $currencySymbol }}<span
                                            class="count">{{ $dataCount['total_commission'] }}</span>
                                    </div>
                                    <div class="name">{{ __('Total Commission') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="centered">
                    <button class="moreless-button-2 site-btn-sm grad-btn">{{ __('Load more') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
