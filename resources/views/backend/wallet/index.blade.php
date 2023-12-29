@extends('backend.layouts.datatable')
@section('title')
    {{ __('All Wallets') }}
@endsection
@section('content')
    <div class="main-content">
        <div class="page-title">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <div class="title-content">
                            <h2 class="title">{{ __('All Wallets') }}</h2>
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
                                {!! $dataTable->table(['class' => 'data-table']) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>    
@endsection

@section('script')

    <script>
        (function ($) {
            "use strict";
        })(jQuery);
    </script>
@endsection

@push('datatable-script')
    {{ $dataTable->scripts() }}
@endpush