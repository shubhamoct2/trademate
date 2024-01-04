@extends('frontend::layouts.user')
@section('title')
    {{ __('History') }}
@endsection
@section('content')    
<div class="row">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="site-card">
            <div class="site-card-header">
                <h3 class="title">{{ __('History') }}</h3>
            </div>
            <div class="site-card-body">
                <div class="site-table">
                    <div class="table-header px-4 py-3">
                        <form action="" method="get">
                            <div class="progress-steps-form">
                                <div class="row align-items-center">
                                    <div class="col-xl-3 col-md-4 col-6">
                                        <div class="input-group">
                                            <select name="year" id="year" class="nice-select site-nice-select">
                                                @foreach($setting['range']['year'] as $year)
                                                <option @if($setting['year'] == $year) selected @endif value="{{$year}}">
                                                    {{ $year }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-md-4 col-6">
                                        <div class="input-group">
                                            <select name="month" id="month" class="nice-select site-nice-select">
                                                @foreach($setting['range']['month'] as $month)
                                                <option @if($setting['month'] == $month) selected @endif value="{{$month}}">
                                                    {{ $month }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-md-4 col-6">
                                        <button type="submit" class="apply-btn">
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
                        <table class="table table-hover history-tbl">
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
                                    <td>{{ $summary_item['monday'] ?? '' }}</td>
                                    <td>{{ $summary_item['tuesday'] ?? '' }}</td>
                                    <td>{{ $summary_item['wednesday'] ?? '' }}</td>
                                    <td>{{ $summary_item['thursday'] ?? '' }}</td>
                                    <td>{{ $summary_item['friday'] ?? '' }}</td>
                                    <td>{{ $summary_item['saturday'] ?? '' }}</td>
                                    <td>{{ $summary_item['sunday'] ?? '' }}</td>
                                    <td>{{ $summary_item['week'] ?? '' }}</td>
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
@endsection

