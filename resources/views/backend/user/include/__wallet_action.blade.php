@can('customer-wallet-manage')
    @if ($status == 0)
    <span type="button" class="enable-wallet">
        <a href="{{ route('admin.user.wallet-enable', $id) }}" class="round-icon-btn red-btn" data-bs-toggle="tooltip" title="Enable" data-bs-original-title="Enable">
            <i class="fas fa-wrench"></i>
        </a>            
    </span>
    @endif
@endcan

<script>
    lucide.createIcons();
    $(document).ajaxComplete(function () {
        "use strict";
        $('[data-bs-toggle="tooltip"]').tooltip({
            "html": true,
        });
    });
</script>
