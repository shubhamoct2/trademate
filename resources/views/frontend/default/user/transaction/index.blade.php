@php use App\Enums\TxnStatus; @endphp
@extends('frontend::layouts.user')
@section('title')
    {{ __('Transaction History') }}
@endsection
@section('content')
    <div class="row">
        <div class="col-xl-12 desktop-screen-show">
            <div class="site-card">
                <div class="site-card-header">
                    <h3 class="title">{{ __('All Transactions') }}</h3>
                </div>
                <div class="site-card-body">
                    <div class="site-table">
                        <div class="table-filter">
                            <div class="filter">
                                <form action="{{ route('user.transactions') }}" method="get">
                                    <div class="search">
                                        <input type="text" id="search" placeholder="Search"
                                               value="{{ request('query') }}"
                                               name="query"/>
                                        <input type="date" name="date" value="{{ request()->get('date') }}"/>
                                        <button type="submit" class="apply-btn"><i
                                                icon-name="search"></i>{{ __('Search') }}</button>
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
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Fee') }}</th>
                                    <th>{{ __('Status') }}</th>                                    
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($transactions as $transaction)
                                    <tr>
                                        <td>
                                            <div class="table-description">
                                                <div class="icon">
                                                    <i icon-name="@switch($transaction->type->value)
                                                                            @case('send_money')arrow-right
                                                                            @break
                                                                            @case('receive_money')arrow-left
                                                                            @break
                                                                            @case('deposit')arrow-down-left
                                                                            @break
                                                                            @case('manual_deposit')arrow-down-left
                                                                            @break
                                                                            @case('investment')arrow-left-right
                                                                            @break
                                                                            @case('withdraw')arrow-up-left
                                                                            @break
                                                                            @default()backpack
                                                                        @endswitch">
                                                    </i>
                                                </div>
                                                <div class="description">
                                                @php
                                                    $re = "/^[0-9]+$/";

                                                    if (!preg_match($re, $transaction->method)) {
                                                        $description = $transaction->description;
                                                    } else {
                                                        $method = intval($transaction->method);

                                                        $from = floor($method / 4);
                                                        $to = $method % 4;

                                                        $from_wallet = '';
                                                        if (0 == $from) {
                                                            $from_wallet = __('Main Wallet');
                                                        } else if (1 == $from) {
                                                            $from_wallet = __('Profit Wallet');
                                                        } else if (2 == $from) {
                                                            $from_wallet = __('Trading Wallet');
                                                        } else if (3 == $from) {
                                                            $from_wallet = __('Commission Wallet');
                                                        }

                                                        $to_wallet = '';
                                                        if (0 == $to) {
                                                            $to_wallet = __('Main Wallet');
                                                        } else if (1 == $to) {
                                                            $to_wallet = __('Profit Wallet');
                                                        } else if (2 == $to) {
                                                            $to_wallet = __('Trading Wallet');
                                                        } else if (3 == $to) {
                                                            $to_wallet = __('Commission Wallet');
                                                        }

                                                        $description = trans('translation.exchange_description', [
                                                            'from' => $from_wallet,
                                                            'to' => $to_wallet,
                                                        ]);
                                                    }
                                                @endphp
                                                    <strong>{{$description}}</strong>
                                                    @if(!in_array($transaction->approval_cause,['none',""]))
                                                        <span class="optional-msg" data-bs-toggle="tooltip" title=""
                                                              data-bs-original-title="{{ $transaction->approval_cause }}">
                                                              <i icon-name="mail"></i>
                                                        </span>
                                                    @endif
                                                    <div class="date">{{ $transaction->created_at }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td><strong>{{ $transaction->tnx }}</strong></td>
                                        <td>
                                            <div
                                                class="site-badge primary-bg">{{ str_replace('_',' ',$transaction->type->value) }}</div>
                                        </td>
                                        <td>
                                            @if ($transaction->type->value == 'send_commission')
                                            <strong class="{{ $transaction->amount > 0 ? 'green-color': 'red-color'}}">{{ ($transaction->amount > 0 ? '+': '' ).$transaction->amount.' '.$currency }}</strong>
                                            @else
                                            <strong class="{{ $transaction->type->value !== 'subtract' 
                                                && $transaction->type->value !== 'investment' 
                                                && $transaction->type->value !== 'send_money' 
                                                && $transaction->type->value !== 'withdraw' ? 'green-color': 'red-color'}}">
                                                {{ ($transaction->type->value !== 'subtract'
                                                    && $transaction->type->value !== 'investment' 
                                                    && $transaction->type->value !== 'send_money'
                                                    && $transaction->type->value !== 'withdraw' ? '+': '-' ) . $transaction->amount. ' ' . ($transaction->pay_currency ? $transaction->pay_currency : $currency ) }}</strong>
                                            @endif
                                        </td>
                                        <td><strong>{{ $transaction->charge }} {{ $currency }}</strong></td>
                                        <td>
                                            @switch($transaction->status->value)
                                                @case('pending')
                                                    <div class="site-badge warnning">{{ __('Pending') }}</div>
                                                    @break
                                                @case('success')
                                                    <div class="site-badge success">{{ __('Success') }}</div>
                                                    @break
                                                @case('failed')
                                                    <div class="site-badge primary-bg">{{ __('Canceled') }}</div>
                                                    @break
                                                @case('rejected')
                                                    <div class="site-badge primary-bg">{{ __('Rejected') }}</div>
                                                    @break
                                            @endswitch
                                        </td>                                        
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            {{  $transactions->links() }}
                        </div>                        
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 mobile-screen-show">
            <!-- Transactions -->
            <div class="all-feature-mobile mobile-transactions mb-3">
                <div class="title">{{ __('All Transactions') }}</div>
                <div class="mobile-transaction-filter">
                    <div class="filter">
                        <form action="{{ route('user.transactions') }}" method="get">
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
                    @foreach($transactions as $transaction )
                        <div class="single-transaction">
                            <div class="transaction-left">
                                <div class="transaction-des">
                                    @php
                                        $re = "/^[0-9]+$/";

                                        if (!preg_match($re, $transaction->method)) {
                                            $description = $transaction->description;
                                        } else {
                                            $method = intval($transaction->method);

                                            $from = floor($method / 4);
                                            $to = $method % 4;

                                            $from_wallet = '';
                                            if (0 == $from) {
                                                $from_wallet = __('Main Wallet');
                                            } else if (1 == $from) {
                                                $from_wallet = __('Profit Wallet');
                                            } else if (2 == $from) {
                                                $from_wallet = __('Trading Wallet');
                                            } else if (3 == $from) {
                                                $from_wallet = __('Commission Wallet');
                                            }

                                            $to_wallet = '';
                                            if (0 == $to) {
                                                $to_wallet = __('Main Wallet');
                                            } else if (1 == $to) {
                                                $to_wallet = __('Profit Wallet');
                                            } else if (2 == $to) {
                                                $to_wallet = __('Trading Wallet');
                                            } else if (3 == $to) {
                                                $to_wallet = __('Commission Wallet');
                                            }

                                            $description = trans('translation.exchange_description', [
                                                'from' => $from_wallet,
                                                'to' => $to_wallet,
                                            ]);
                                        }
                                    @endphp
                                    <div class="transaction-title">{{ $description }}
                                    </div>
                                    <div class="transaction-id">{{ $transaction->tnx }}</div>
                                    <div class="transaction-date">{{ $transaction->created_at }}</div>
                                    <div class="mt-2">
                                        <div class="site-badge primary-bg">{{ str_replace('_',' ',$transaction->type->value) }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="transaction-right">
                                @if ($transaction->type->value == 'send_commission')
                                    <div
                                        class="transaction-amount {{ $transaction->amount > 0 ? 'add': 'sub' }}">
                                        {{ ($transaction->amount > 0 ? '+': '' ).$transaction->amount.' '.$currency }}
                                    </div>
                                @else
                                    <div
                                        class="transaction-amount {{ $transaction->type->value !== 'subtract' 
                                                && $transaction->type->value !== 'investment' 
                                                && $transaction->type->value !== 'send_money' 
                                                && $transaction->type->value !== 'withdraw' ? 'add': 'sub' }}">
                                        {{ ($transaction->type->value !== 'subtract'
                                            && $transaction->type->value !== 'investment' 
                                            && $transaction->type->value !== 'send_money'
                                            && $transaction->type->value !== 'withdraw' ? '+': '-' ) . $transaction->amount. ' ' . ($transaction->pay_currency ? $transaction->pay_currency : $currency ) }}
                                    </div>
                                @endif
                                <div class="transaction-fee sub">
                                    -{{  $transaction->charge.' '. $currency .' '.__('Fee') }} </div>
                                @if($transaction->status->value == App\Enums\TxnStatus::Pending->value)
                                    <div class="transaction-status pending">{{ __('Pending') }}</div>
                                @elseif($transaction->status->value ==  App\Enums\TxnStatus::Success->value)
                                    <div class="transaction-status success">{{ __('Success') }}</div>
                                @elseif($transaction->status->value ==  App\Enums\TxnStatus::Failed->value)
                                    <div class="transaction-status canceled">{{ __('Canceled') }}</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                {{  $transactions->onEachSide(1)->links() }}
            </div>
        </div>
    </div>
@endsection
