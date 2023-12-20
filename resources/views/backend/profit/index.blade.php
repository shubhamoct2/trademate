@extends('backend.layouts.app')
@section('title')
    {{ __('Profit Wallet Admin') }}
@endsection
@section('content')
    <div class="main-content">
        <div class="page-title">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <div class="title-content">
                            <h2 class="title">{{ __('Profit Wallet Admin') }} </h2>
                            <a href="{{ url()->previous() }}" class="title-btn"><i
                                    icon-name="corner-down-left"></i>{{ __('Back') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid">
                <div class="site-card">
                    <div class="site-card-header">
                        <h4 class="title">{{ __('Daily Profit Distribution') }}</h4>
                    </div>
                    <div class="site-card-body">
                        <form action="{{ route('admin.profit.push') }}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 mx-auto">
                                    <div class="site-input-groups">
                                        <label for="send_profit" class="box-input-label">{{ __('Please Input Daily Profit') }}</label>
                                        <input id="send_profit" name="amount" type="text" class="box-input" value="" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 mx-auto">
                                    <div class="site-input-groups mb-0">
                                        <label class="box-input-label"
                                            for="">{{ __('Method:') }}</label>
                                        <div class="switch-field same-type">
                                            <input
                                                type="radio"
                                                id="distribute-daily"
                                                name="method"
                                                value="1"
                                                checked
                                            />
                                            <label for="distribute-daily">{{ __('6 PM (Daily)') }}</label>
                                            <input
                                                type="radio"
                                                id="distribute-now"
                                                name="method"
                                                value="0"
                                            />
                                            <label for="distribute-now">{{ __('Now') }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 mx-auto">
                                    <button type="submit" class="site-btn-sm primary-btn w-100 centered mb-4">{{ __('Send') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="site-card">
                    <div class="site-card-header">
                        <h4 class="title">{{ __('Today Scheduled Profit Distribution') }}</h4>
                        <div>{{ __('Current') . ': ' . $current_datetime }}</div>
                    </div>
                    <div class="site-card-body table-responsive">
                        <div class="site-datatable">
                            <table id="dataTable" class="display data-table">
                                <thead>
                                <tr>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Method') }}</th>
                                    <th>{{ __('Status') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
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

            var table = $('#dataTable').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax: "{{ route('admin.profit.today-list') }}",
                columns: [
                    {data: 'datetime', name: 'created_at'},
                    {data: 'type', name: 'type'},
                    {data: 'amount', name: 'amount'},
                    {data: 'method', name: 'method'},
                    {data: 'status', name: 'status'},
                ],
                order: [[0, 'desc']]
            });

        })(jQuery);
    </script>
@endsection
