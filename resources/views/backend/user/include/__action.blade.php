@canany(['customer-basic-manage','customer-balance-add-or-subtract','customer-change-password','all-type-status'])
    <a href="{{route('admin.user.login',$id)}}" class="round-icon-btn red-btn" data-bs-toggle="tooltip"
       title="Login As User" data-bs-original-title="Login As User"><i icon-name="user-plus"></i></a>
    <a href="{{route('admin.user.edit',$id)}}" class="round-icon-btn primary-btn" data-bs-toggle="tooltip"
       title="Edit User" data-bs-original-title="Edit User"><i icon-name="edit-3"></i></a>
@endcanany
@can('customer-mail-send')
    <span type="button"
          data-id="{{$id}}"
          data-name="{{ $first_name.' '. $last_name }}"
          class="send-mail"
    ><button class="round-icon-btn red-btn" data-bs-toggle="tooltip" title="Send Email"
             data-bs-original-title="Send Email"><i icon-name="mail"></i></button></span>
@endcan
@can('customer-account-delete')
    <span type="button"
          data-id="{{$id}}"
          data-name="{{ $first_name.' '. $last_name }}"
          class="delete-user"
    >
        <button class="round-icon-btn red-btn" data-bs-toggle="tooltip" title="Delete User" data-bs-original-title="Delete User">
            <i class="fas fa-user-times"></i>
        </button>            
    </span>
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
