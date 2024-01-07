@extends('backend.layouts.app')
@section('title')
    {{ __('History Calendar Admin') }}
@endsection
@section('content')
    <div class="main-content">
        <div class="page-title">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <div class="title-content">
                            <h2 class="title">{{ __('History Calendar Admin') }}</h2>
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
                                <div class="table-header px-4 py-3">
                                    <form action="" method="get">
                                        <div class="progress-steps-form">
                                            <div class="row align-items-center">
                                                <div class="col-xl-3 col-md-4 col-6">
                                                    <div class="site-input-groups">
                                                        <label class="box-input-label" for="">{{ __('Year') }}:</label>
                                                        <select name="year" class="form-select" id="year">
                                                            @foreach($setting['range']['year'] as $year)
                                                            <option @if($setting['year'] == $year) selected @endif value="{{$year}}">
                                                                {{ $year }}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-xl-3 col-md-4 col-6">
                                                    <div class="site-input-groups">
                                                        <label class="box-input-label" for="">{{ __('Month') }}:</label>
                                                        <select name="month" class="form-select" id="month">
                                                            @foreach($setting['range']['month'] as $month)
                                                            <option @if($setting['month'] == $month) selected @endif value="{{$month}}">
                                                                {{ $month }}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-xl-3 col-md-4 col-6">
                                                    <button type="submit" class="site-btn primary-btn w-100">
                                                        <i icon-name="search"></i>
                                                        {{ __('Search') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="table-responsive p-3">
                                    @foreach ($summary_month as $week_number => $summary_week)
                                    <table class="display data-table history-table">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <div>{{ __('Description') }}</div>
                                                </th>
                                                <th>
                                                    <div>{{ __('Monday') }}</div>
                                                    <div>({{ $summary_week['date'][0] }})</div>
                                                </th>
                                                <th>
                                                    <div>{{ __('Tuesday') }}</div>
                                                    <div>({{ $summary_week['date'][1] }})</div>
                                                </th>   
                                                <th>
                                                    <div>{{ __('Wednesday') }}</div>
                                                    <div>({{ $summary_week['date'][2] }})</div>
                                                </th>
                                                <th>
                                                    <div>{{ __('Thursday') }}</div>
                                                    <div>({{ $summary_week['date'][3] }})</div>
                                                </th>
                                                <th>
                                                    <div>{{ __('Friday') }}</div>
                                                    <div>({{ $summary_week['date'][4] }})</div>
                                                </th>
                                                <th>
                                                    <div>{{ __('Saturday') }}</div>
                                                    <div>({{ $summary_week['date'][5] }})</div>
                                                </th>
                                                <th>
                                                    <div>{{ __('Sunday') }}</div>
                                                    <div>({{ $summary_week['date'][6] }})</div>
                                                </th>
                                                <th>
                                                    <div>{{ __('Week Number') }}</div>
                                                    <div>({{ $week_number }})</div>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>                            
                                            @foreach ($summary_week['list'] as $summary_item)
                                            <tr class="{{ isset($summary_item['class']) ? $summary_item['class'] : '' }}">
                                                <td>{{ $summary_item['name'] ?? '' }}</td>
                                                <td>{{ $summary_item['monday'] ? '$ ' . $summary_item['monday'] : '' }}</td>
                                                <td>{{ $summary_item['tuesday'] ? '$ ' . $summary_item['tuesday'] : '' }}</td>
                                                <td>{{ $summary_item['wednesday'] ? '$ ' . $summary_item['wednesday'] : '' }}</td>
                                                <td>{{ $summary_item['thursday'] ? '$ ' . $summary_item['thursday'] : '' }}</td>
                                                <td>{{ $summary_item['friday'] ? '$ ' . $summary_item['friday'] : '' }}</td>
                                                <td>{{ $summary_item['saturday'] ? '$ ' . $summary_item['saturday'] : '' }}</td>
                                                <td>{{ $summary_item['sunday'] ? '$ ' . $summary_item['sunday'] : '' }}</td>
                                                <td>{{ $summary_item['week'] ? '$ ' . $summary_item['week'] : '' }}</td>
                                            </tr>
                                            @endforeach                                                         
                                        </tbody>
                                    </table>
                                    @endforeach   
                                </div>
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
