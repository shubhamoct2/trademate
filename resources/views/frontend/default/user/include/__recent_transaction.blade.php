<div class="row">
    <div class="col-xl-12">
        <div class="site-card">
            <div class="site-card-header">
                <h3 class="title">{{ __('Recent Transactions') }}</h3>
            </div>
            <div class="site-card-body table-responsive">
                <div class="site-datatable">
                    <table class="display data-table">
                        <thead>
                        <tr>
                            <th>{{ __('Description') }}</th>
                            <th>{{ __('Transactions ID') }}</th>
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('Amount') }}</th>
                            <th>{{ __('Fee') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Method') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($recentTransactions as $transaction )
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
                                            @case('investment')arrow-left-right
                                            @break
                                            @case('withdraw')arrow-up-left
                                            @break
                                            @default()backpack
                                         @endswitch">
                                            </i>
                                        </div>


                                        <div class="description">
                                            <strong>{{ $transaction->description }} @if(!in_array($transaction->approval_cause,['none',""]))
                                                    <span class="optional-msg" data-bs-toggle="tooltip" title=""
                                                          data-bs-original-title="{{ $transaction->approval_cause }}"><i
                                                            icon-name="mail"></i></span>
                                                @endif
                                            </strong>
                                            <div class="date">{{ $transaction->created_at }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td><strong>{{ $transaction->tnx }}</strong></td>
                                <td>
                                    <div
                                        class="site-badge primary-bg">{{  ucfirst(str_replace('_',' ',$transaction->type->value ))  }}</div>
                                </td>
                                <td>
                                @if ($transaction->type->value == 'send_commission')
                                    <strong class="{{ $transaction->amount > 0 ? 'green-color': 'red-color'}}">{{ ($transaction->amount > 0 ? '+': '' ).$transaction->amount.' '.$currency }}</strong>
                                @else
                                    <strong class="{{ txn_type($transaction->type->value, ['green-color','red-color']) }}">
                                            {{ txn_type($transaction->type->value,['+','-']) .$transaction->final_amount.' '. ($transaction->pay_currency ? $transaction->pay_currency : $currency) }}
                                        </strong>
                                    </td>
                                @endif                                
                                </td>
                                <td><strong>{{ $transaction->charge.' '. $currency }}</strong></td>
                                <td>


                                    @if($transaction->status->value == \App\Enums\TxnStatus::Pending->value)
                                        <div class="site-badge warnning">{{ __('Pending') }}</div>
                                    @elseif($transaction->status->value ==  \App\Enums\TxnStatus::Success->value)
                                        <div class="site-badge success">{{ __('Success') }}</div>
                                    @elseif($transaction->status->value ==  \App\Enums\TxnStatus::Rejected->value)
                                        <div class="site-badge primary-bg">{{ __('Rejected') }}</div>
                                    @elseif($transaction->status->value ==  \App\Enums\TxnStatus::Failed->value)
                                        <div class="site-badge primary-bg">{{ __('Canceled') }}</div>
                                    @endif
                                </td>
                                @php
                                    $re = "/^[0-9]+$/";

                                    if (!preg_match($re, $transaction->method)) {
                                        $method = $transaction->method;
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

                                        $method = trans('translation.exchange_description', [
                                            'from' => $from_wallet,
                                            'to' => $to_wallet,
                                        ]);
                                    }
                                @endphp
                                <td><strong>{{ $method }}</strong></td>
                            </tr>
                        @endforeach


                        @if($recentTransactions->isEmpty())
                            <tr class="centered">
                                <td colspan="7">{{ __('No Data Found') }}</td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
