<div
    class="modal fade"
    id="account_delete"
    tabindex="-1"
    aria-labelledby="account_delete"
    aria-hidden="true"
>
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content site-table-modal">
            <div class="modal-body popup-body">
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                ></button>
                <div class="popup-body-text">
                    <h3 class="title mb-4"> {{ __('Are you sure to delete the account of ') }} <span id="name">{{ $name ?? ''}}</span></h3>
                    <form action="" method="post" id="account_delete_form">
                        @csrf
                        <input type="hidden" name="id" value="{{ $id ?? 0 }}" id="userId">
                        <div class="action-btns">
                            <button type="submit" class="site-btn-sm primary-btn me-2">
                                <i icon-name="send"></i>
                                {{ __('Delete') }}
                            </button>
                            <a
                                href="#"
                                class="site-btn-sm red-btn"
                                data-bs-dismiss="modal"
                                aria-label="Close"
                            >
                                <i icon-name="x"></i>
                                {{ __('Cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
