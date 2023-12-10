<div
    class="modal fade"
    id="review_transaction"
    tabindex="-1"
    aria-labelledby="reviewTransactionModalLabel"
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
                    <h3 class="title mb-4"> {{ __('Review Exchange Transaction') }} <span id="name">{{ $name ?? ''}}</span></h3>
                    <form action="{{route('admin.exchange.update')}}" method="post" id="review_transaction_form">
                        @csrf

                        <input type="hidden" name="review_tnx" value="" id="review_tnx">
                        <input type="hidden" name="review_type" value="approve" id="review_type">

                        <div class="site-input-groups">
                            <label for="" class="box-input-label">{{ __('User:') }}</label>
                            <div class="" id="review_user_name"></div>
                        </div>

                        <div class="site-input-groups">
                            <label for="" class="box-input-label">{{ __('Tx ID:') }}</label>
                            <div class="" id="review_transaction_id"></div>
                        </div>

                        <div class="site-input-groups">
                            <label for="" class="box-input-label">{{ __('Type:') }}</label>
                            <div class="" id="review_transaction_type"></div>
                        </div>

                        <div class="site-input-groups">
                            <label for="" class="box-input-label">{{ __('Type:') }}</label>
                            <div class="" id="review_transaction_amount"></div>
                        </div>

                        <div class="action-btns">
                            <button id="approve" type="submit" class="site-btn-sm primary-btn me-2">
                                <i icon-name="check"></i>
                                {{ __('Approve') }}
                            </button>
                            <button id="reject" type="submit" class="site-btn-sm yellow-btn me-2">
                                <i icon-name="ban"></i>
                                {{ __('Reject') }}
                            </button>
                            <a
                                href="#"
                                class="site-btn-sm red-btn"
                                data-bs-dismiss="modal"
                                aria-label="Close"
                            >
                                <i icon-name="x"></i>
                                {{ __('Close') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
