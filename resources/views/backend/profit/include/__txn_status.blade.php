@switch($status)
    @case('pending')
        <div class="site-badge pending">{{ __('Pending') }}</div>
        @break
    @case('success')
        <div class="site-badge success">{{ __('Success') }}</div>
        @break
    @case('failed')
        <div class="site-badge danger">{{ __('Cancelled') }}</div>
        @break
    @case('rejected')
        <div class="site-badge danger">{{ __('Rejected') }}</div>
        @break
@endswitch
