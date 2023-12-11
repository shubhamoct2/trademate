@extends('backend.deposit.index')
@section('title')
    {{ __('Deposit History') }}
@endsection
@section('style')
    <link href="{{ asset('backend/css/codemirror.css') }}" rel='stylesheet'>
    <link href="{{ asset('backend/css/ayu-dark.css') }}" rel='stylesheet'>
@endsection
@section('deposit_content')
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


