<div class="row">
    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
        <div class="data-card">
            <div class="icon">
                <i icon-name="users"></i>
            </div>
            <div class="content">
                <h4 class="count">{{ $data['register_user'] }}</h4>
                <p>{{ __('Registered User') }}</p>
            </div>
            <a class="link" href="{{ route('admin.user.index') }}"><i icon-name="external-link"></i></a>
        </div>
    </div>
    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
        <div class="data-card">
            <div class="icon">
                <i icon-name="user-check"></i>
            </div>
            <div class="content">
                <h4 class="count">{{ $data['active_user'] }}</h4>
                <p>{{ __('Active Users') }}</p>
            </div>
            <a class="link" href="{{ route('admin.user.active') }}"><i icon-name="external-link"></i></a>
        </div>
    </div>
    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
        <div class="data-card">
            <div class="icon">
                <i icon-name="user-cog"></i>
            </div>
            <div class="content">
                <h4 class="count">{{ $data['total_staff'] }}</h4>
                <p>{{ __('Site Staff') }}</p>
            </div>
            <a class="link" href="{{ route('admin.staff.index') }}"><i icon-name="external-link"></i></a>
        </div>
    </div>
    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
        <div class="data-card">
            <div class="icon">
                <i icon-name="wallet"></i>
            </div>
            <div class="content">
                <h4>{{ $currencySymbol }}<span class="count">{{ round($data['total_deposit'],2) }}</span></h4>
                <p>{{ __('Total Deposits') }}</p>
            </div>
            <a class="link" href="{{ route('admin.deposit.history') }}"><i icon-name="external-link"></i></a>
        </div>
    </div>
    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
        <div class="data-card">
            <div class="icon">
                <i icon-name="landmark"></i>
            </div>
            <div class="content">
                <h4>{{ $currencySymbol }}<span class="count">{{ round($data['total_withdraw'],2) }}</span></h4>
                <p>{{ __('Total Withdraw') }}</p>
            </div>
            <a class="link" href="{{ route('admin.withdraw.history') }}"><i icon-name="external-link"></i></a>
        </div>
    </div>
    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
        <div class="data-card">
            <div class="icon">
                <i icon-name="receipt"></i>
            </div>
            <div class="content">
                <h4>{{ $currencySymbol }}<span class="count">{{ round($data['total_trading'],2) }}</span></h4>
                <p>{{ __('TRADING WALLET MASTER') }}</p>
            </div>
            <a class="link" href="{{ route('admin.user.index') }}"><i icon-name="external-link"></i></a>
        </div>
    </div>
    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
        <div class="data-card">
            <div class="icon">
                <i icon-name="link"></i>
            </div>
            <div class="content">
                <h4 class="count">{{ $data['total_referral'] }}</h4>
                <p>{{ __('Total Referral') }}</p>
            </div>
            <a class="link" href="{{ route('admin.referral.index') }}"><i icon-name="external-link"></i></a>
        </div>
    </div>
    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
        <div class="data-card">
            <div class="icon">
                <i icon-name="send"></i>
            </div>
            <div class="content">
                <h4>{{ $currencySymbol }}<span class="count">{{ round($data['total_send'],2) }}</span></h4>
                <p>{{ __('Total Send') }}</p>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
        <div class="data-card">
            <div class="icon">
                <i icon-name="webhook"></i>
            </div>
            <div class="content">
                <h4 class="count">{{ $data['total_gateway'] }}</h4>
                <p>{{ __('Total Automatic Gateway') }}</p>
            </div>
            <a class="link" href="{{ route('admin.gateway.automatic') }}"><i icon-name="external-link"></i></a>
        </div>
    </div>

    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
        <div class="data-card">
            <div class="icon">
                <i icon-name="help-circle"></i>
            </div>
            <div class="content">
                <h4 class="count">{{ $data['total_ticket'] }}</h4>
                <p>{{ __('Total Ticket') }}</p>
            </div>
            <a class="link" href="{{ route('admin.ticket.index') }}"><i icon-name="external-link"></i></a>
        </div>
    </div>

    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
        <div class="data-card">
            <div class="icon">
                <i icon-name="help-circle"></i>
            </div>
            <div class="content">
                <h4 class="count">{{ $data['pending_client'] }}</h4>
                <p>{{ __('Pending Client') }}</p>
            </div>
            <a class="link" href="{{ route('admin.ticket.index') }}"><i icon-name="external-link"></i></a>
        </div>
    </div>

    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
        <div class="data-card">
            <div class="icon">
                <i icon-name="help-circle"></i>
            </div>
            <div class="content">
                <h4 class="count">{{ $data['pending_support'] }}</h4>
                <p>{{ __('Pending Support') }}</p>
            </div>
            <a class="link" href="{{ route('admin.ticket.index') }}"><i icon-name="external-link"></i></a>
        </div>
    </div>    

    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
        <div class="data-card">
            <div class="icon">
                <i class="far fa-credit-card"></i>
            </div>
            <div class="content">
                <h4 class="count">{{ $data['total_pending_kyc'] }}</h4>
                <p>{{ __('Pending KYC') }}</p>
            </div>
            <a class="link" href="{{ route('admin.kyc.pending') }}"><i icon-name="external-link"></i></a>
        </div>
    </div>

    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
        <div class="data-card">
            <div class="icon">
                <i class="fas fa-money-check-alt"></i>
            </div>
            <div class="content">
                <h4 class="count">{{ $data['total_pending_transaction'] }}</h4>
                <p>{{ __('Pending Transaction') }}</p>
            </div>
            <a class="link" href="{{ route('admin.exchange.list') }}"><i icon-name="external-link"></i></a>
        </div>
    </div>

</div>
