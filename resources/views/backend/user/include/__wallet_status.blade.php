@switch($status)
    @case(1)
        <div class="site-badge pending">{{ __('Enabled') }}</div>
        @break
    @case(0)
        <div class="site-badge success">{{ __('Disabled') }}</div>
        @break
@endswitch
