@switch($status)
    @case(0)
        <div class="site-badge danger">{{ __('Draft') }}</div>
        @break
    @case(1)
        <div class="site-badge success">{{ __('Verified') }}</div>
        @break
    @case(2)
        <div class="site-badge pending">{{ __('Pending') }}</div>
        @break
    @case(3)
        <div class="site-badge danger">{{ __('Rejected') }}</div>
        @break
@endswitch
