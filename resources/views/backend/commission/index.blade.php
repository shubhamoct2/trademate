@extends('backend.layouts.app')
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
                            <h2 class="title">{{ __('All Commission History') }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-12">
                    <div class="site-card">
                        <div class="site-card-body table-responsive">
                            <div class="site-datatable">
                                <table id="dataTable" class="display data-table">
                                    <thead>
                                    <tr>
                                        <th>{{ __('Date') }}</th>
                                        <th>{{ __('User') }}</th>
                                        <th>{{ __('Tnx') }}</th>
                                        <th>{{ __('Description') }}</th>
                                        <th>{{ __('Amount') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Action') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- Modal for Review Transaction -->
                    @can('internal-transfer-manage')
                        @include('backend.commission.include.__review_transaction')
                    @endcan
                    <!-- Modal for Review Transaction-->
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        (function ($) {
            "use strict";

            var table = $('#dataTable').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax: "{{ route('admin.commission.list') }}",
                columns: [
                    {data: 'created_at', name: 'created_at'},
                    {data: 'username', name: 'username'},
                    {data: 'tnx', name: 'tnx'},
                    {data: 'description', name: 'type'},
                    {data: 'final_amount', name: 'final_amount'},
                    {data: 'status', name: 'status'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            });

            // review transaction modal form open
            $('body').on('click', '.review-transaction', function () {
                var name = $(this).data('name');
                var tnx = $(this).data('tnx');
                var type = $(this).data('type');
                var amount = $(this).data('amount');

                $('#review_user_name').html(name);
                $('#review_transaction_id').html(tnx);
                $('#review_transaction_type').html(type);
                $('#review_transaction_amount').html(amount);
                $('#review_tnx').val(tnx);

                $('#review_transaction').modal('toggle')
            });

            // approve
            $('body').on('click', '#approve', function () {
                $('#review_type').val("approve");
            });

            $('body').on('click', '#reject', function () {
                $('#review_type').val("reject");
            });


        })(jQuery);
    </script>
@endsection
