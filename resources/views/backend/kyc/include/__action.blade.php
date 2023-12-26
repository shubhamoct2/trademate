@can('kyc-action')
  <a href="{{ route('admin.kyc.download', $id)}}" target="_blank" class="round-icon-btn red-btn" data-bs-toggle="tooltip"
       title="Login As User" data-bs-original-title="Login As User"><i icon-name="book"></i></a>
  <span data-bs-toggle="tooltip" title="" data-bs-placement="top" data-bs-original-title="View KYC Details">
    <button
        class="round-icon-btn primary-btn"
        type="button"
        id="action-kyc"
        data-id="{{$id}}"
    >
      <i icon-name="edit-3"></i>
    </button>
  </span>
  <script>
    'use strict';
    lucide.createIcons();
  </script>
@endcan


