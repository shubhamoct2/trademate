@extends('backend.withdraw.index')
@section('title')
    {{ __('Withdraw History') }}
@endsection
@section('withdraw_content')
    <div class="col-xl-12 col-md-12">
        <div class="site-card">
            <div class="site-card-body table-responsive">
                <div class="site-datatable">
                    {!! $dataTable->table(['class' => 'data-table']) !!}
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
                ajax: "{{ route('admin.withdraw.history') }}",
                columns: [
                    {data: 'created_at', name: 'created_at'},
                    {data: 'username', name: 'username'},
                    {data: 'tnx', name: 'tnx'},
                    {data: 'amount', name: 'amount'},
                    {data: 'charge', name: 'charge'},
                    {data: 'method', name: 'method'},
                    {data: 'status', name: 'status'},
                ]
            });


        })(jQuery);
    </script>
@endsection
@push('datatable-script')
    {{ $dataTable->scripts() }}
@endpush
