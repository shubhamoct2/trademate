@extends('frontend::layouts.user')
@section('title')
    {{ __('Withdraw Logs') }}
@endsection
@section('content')
    <div class="row">
        @if(!$isMobile)
            <div class="col-xl-12">
                <div class="site-card">
                    <div class="site-card-header">
                        <h3 class="title">{{ __('All Withdraw Log') }}</h3>
                    </div>
                    <div class="site-card-body">
                        <div class="site-table">
                            <div class="table-filter">
                                <div class="filter">
                                    <form action="{{ route('user.withdraw.log') }}" method="get">
                                        <div class="search">
                                            <input type="text" id="search" placeholder="Search"
                                                   value="{{ request('query') }}"
                                                   name="query"/>
                                            <input type="date" name="date" value="{{ request()->get('date') }}"/>
                                            <button type="submit" class="apply-btn"><i
                                                    icon-name="search"></i>{{ __('Search') }}</button>
                                            <button type="button" class="apply-btn reset-btn ml-2"><i
                                                icon-name="eraser"></i>{{ __('Reset') }}</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th>{{ __('Description') }}</th>
                                        <th>{{ __('Transactions ID') }}</th>
                                        <th>{{ __('Amount') }}</th>
                                        <th>{{ __('Fee') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Method') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($withdraws as $raw)
                                        <tr>
                                            <td>
                                                <div class="table-description">
                                                    <div class="icon">
                                                        <i icon-name="arrow-up-left"></i>
                                                    </div>
                                                    <div class="description">
                                                        <strong>{{$raw->description}}</strong>@if(!in_array($raw->approval_cause,['none',""]))
                                                            <span class="optional-msg" data-bs-toggle="tooltip" title=""
                                                                  data-bs-original-title="{{ $raw->approval_cause }}"><i
                                                                    icon-name="mail"></i></span>
                                                        @endif
                                                        <div class="date">{{ $raw->created_at }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><strong>{{ $raw->tnx }}</strong></td>
                                            @if ($raw->method == 'Alphapo')
                                                @if ($raw->txID)
                                                    <td><strong class="green-color">-{{$raw->final_amount.' '.$raw->pay_currency }}</strong>
                                                @else 
                                                    <td><strong class="green-color">-{{$raw->amount.' '.$raw->pay_currency }}</strong>
                                                @endif
                                            @else
                                            <td><strong class="green-color">-{{$raw->amount.' '.$currency }}</strong>
                                            @endif
                                            <td><strong class="red-color">-{{ $raw->charge }} {{ $currency }}</strong>
                                            </td>
                                            <td>
                                                @switch($raw->status->value)
                                                    @case('pending')
                                                        <div class="site-badge warnning">{{ __('Pending') }}</div>
                                                        @break
                                                    @case('success')
                                                        <div class="site-badge success">{{ __('Success') }}</div>
                                                        @break
                                                    @case('failed')
                                                        <div class="site-badge primary-bg">{{ __('canceled') }}</div>
                                                        @break
                                                @endswitch
                                            </td>
                                            <td><strong>{{ ucfirst($raw->method) }}</strong></td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                {{  $withdraws->links() }}
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        @else
            <div class="col-12">
                <!-- Transactions -->
                <div class="all-feature-mobile mobile-transactions mb-3">
                    <div class="title">{{ __('All Withdraw Log') }}</div>
                    <div class="mobile-transaction-filter">
                        <div class="filter">
                            <form action="{{ route('user.withdraw.log') }}" method="get">
                                <div class="search">

                                    <input type="text" placeholder="Search" value="{{ request('query') }}"
                                           name="query"/>
                                    <input type="date" name="date" value="{{ request()->get('date') }}"/>
                                    <button type="submit" class="apply-btn"><i icon-name="search"></i></button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="contents">
                        @foreach($withdraws as $raw )
                            <div class="single-transaction">
                                <div class="transaction-left">
                                    <div class="transaction-des">
                                        <div class="transaction-title">{{ $raw->description }}
                                        </div>
                                        <div class="transaction-id">{{ $raw->tnx }}</div>
                                        <div class="transaction-date">{{ $raw->created_at }}</div>
                                    </div>
                                </div>
                                <div class="transaction-right">
                                    <div
                                        class="transaction-amount sub">
                                        -{{$raw->final_amount.' '.$raw->pay_currency }}</div>
                                    <div class="transaction-fee sub">
                                        -{{  $raw->charge.' '. $currency .' '.__('Fee') }} </div>
                                    <div class="transaction-gateway">{{ $raw->method }}</div>

                                    @if($raw->status->value == App\Enums\TxnStatus::Pending->value)
                                        <div class="transaction-status pending">{{ __('Pending') }}</div>
                                    @elseif($raw->status->value ==  App\Enums\TxnStatus::Success->value)
                                        <div class="transaction-status success">{{ __('Success') }}</div>
                                    @elseif($raw->status->value ==  App\Enums\TxnStatus::Failed->value)
                                        <div class="transaction-status canceled">{{ __('canceled') }}</div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    {{  $withdraws->onEachSide(1)->links() }}
                </div>

            </div>
        @endif
    </div>

@endsection
@section('script')
    <script>
        $(".reset-btn").on('click', function (e) {
            e.preventDefault();

            $(this).closest('form').find("input[type=text], input[type=date]").val("");
            $(this).closest('form').submit();
        });
    </script>
@endsection
