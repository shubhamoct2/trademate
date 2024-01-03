@can('kyc-action')
  <a href="{{ route('admin.kyc.download', $id)}}" target="_blank" class="round-icon-btn red-btn" data-bs-toggle="tooltip"
    title="Download KYC" data-bs-original-title="Download KYC">
    <i class="fas fa-download"></i>
  </a>
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
  <a href="{{ route('admin.kyc.draft', $id)}}" class="round-icon-btn red-btn" data-bs-toggle="tooltip"
    title="Update to Draft" data-bs-original-title="Update to Draft">
    <i class="fas fa-wrench"></i>
  </a>
  <a href="{{route('admin.user.login',$id)}}" class="round-icon-btn red-btn" data-bs-toggle="tooltip"
       title="Login As User" data-bs-original-title="Login As User"><i icon-name="user-plus"></i></a>
  <script>
    'use strict';
    lucide.createIcons();
  </script>
@endcan


