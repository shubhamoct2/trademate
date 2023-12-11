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
@push('datatable-script')
    {{ $dataTable->scripts() }}
@endpush
