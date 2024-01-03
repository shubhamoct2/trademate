@extends('backend.layouts.datatable')
@section('title')
    {{ __('Transactions') }}
@endsection
@section('content')
    <div class="main-content">
        <div class="page-title">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <div class="title-content">
                            <h2 class="title">{{ __('All Transactions') }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-12">
                    <div class="site-card">
                        <button type="button" class="site-btn-sm primary-btn centered mb-4 delete-all-transaction">{{ __('Delete All History') }}</button>
                        <div class="site-card-body table-responsive">
                            <div class="site-datatable">                            
                                {!! $dataTable->table(['class' => 'data-table']) !!}
                            </div>
                        </div>
                    </div>
                    <!-- Modal for Delete Transaction -->
                    <div class="modal fade"
                        id="review_delete_transaction"
                        tabindex="-1"
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
                                        <h3 class="title mb-4">{{ __('Do you want to delete all transactions?') }}</h3>
                                        <form action="{{ route('admin.transactions.delete') }}" method="post" id="review_delete_transaction_form">
                                            @csrf
                                            <div class="action-btns">
                                                <button id="approve" type="submit" class="site-btn-sm primary-btn me-2">
                                                    <i icon-name="check"></i>
                                                    {{ __('Yes') }}
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
                    <!-- Modal for Delete Transaction-->
                </div>
            </div>
        </div>        
    </div>
@endsection
@push('datatable-script')
    {{ $dataTable->scripts() }}
@endpush

@section('script')
    <script>
        (function ($) {
            "use strict";

            // delete transaction modal form open
            $('body').on('click', '.delete-all-transaction', function () {
                $('#review_delete_transaction').modal('toggle');
            });

        })(jQuery);
    </script>
@endsection
