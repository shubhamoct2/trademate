<div class="bottom-appbar">
    <a href="{{ route('user.dashboard') }}" class="{{ isActive('user.dashboard') }}">
        <i icon-name="layout-dashboard"></i>
    </a>
    <a href="{{ route('user.deposit.amount') }}" class="{{ isActive('user.deposit*') }}">
        <i icon-name="download"></i>
    </a>
    <a href="{{ route('user.transactions') }}" class="{{ isActive('user.transactions*') }}">
        <i icon-name="cast"></i>
    </a>
    <a href="{{ route('user.send-money.view') }}" class="{{ isActive('user.send-money.*') }}">
        <i icon-name="box"></i>
    </a>
    <a href="{{ route('user.withdraw.view') }}" class="{{ isActive('user.withdraw.*') }}">
        <i icon-name="receipt"></i>
    </a>
    <a href="{{ route('user.referral') }}" class="{{ isActive('user.referral*') }}">
        <i icon-name="gift"></i>
    </a>
    <a href="{{ route('user.setting.show') }}" class="{{ isActive('user.setting*') }}">
        <i icon-name="settings"></i>
    </a>
    <a href="{{ route('user.history.show') }}" class="{{ isActive('user.history*') }}">
        <i icon-name="history"></i>
    </a>
    <a href="{{ route('user.ticket.index') }}" class="{{ isActive('user.ticket*') }}">
        <i icon-name="ticket"></i>
    </a>
    <a href="{{ route('user.notification.all') }}" class="{{ isActive('user.notification*') }}">
        <i icon-name="alarm-check"></i>
    </a>
</div>
