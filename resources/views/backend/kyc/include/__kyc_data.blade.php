<h3 class="title mb-4">
    {{ __('KYC Details') }}
</h3>

<div>
    <ul class="list-group mb-4">
        <li class="list-group-item">
            {{ __('KYC Type') }}:
            <strong>{{ ucwords($kycInfo->data['kyc_type']) }}</strong>
        </li>
    </ul>
</div>

<div>
    @if ($kycInfo->data['kyc_type'] == "individual")
    <h4 class="step-title">{{ __('General information') }}</h4>
    <ul class="list-group mb-4">                        
        @if (isset($kycInfo->data['general']))
            <li class="list-group-item">
                {{ __('Name of the relationship') }}:
                <strong>{{ getDescriptionString($kycInfo->data['general']['relationship_name']) }}</strong>
            </li>
            <li class="list-group-item">
                {{ __('Date of the KYC') }}:
                <strong>{{ $kycInfo->data['kyc_date'] }}</strong>
            </li>
            <li class="list-group-item">
                {{ __('Recommended by') }}:
                <strong>{{ $kycInfo->data['general']['recommended_by'] }}</strong>
            </li>
            <li class="list-group-item">
                {{ __('Summary of the background of the relationship') }}:
                <strong>{{ getDescriptionString($kycInfo->data['general']['summary_relation']) }}</strong>
            </li>
        @endif                        
    </ul>
    @else
    <h4 class="step-title">{{ __('Business Relation Profile Corporate') }}</h4>
    <ul class="list-group mb-4">                        
        @if (isset($kycInfo->data['general']))
            <li class="list-group-item">
                {{ __('Name of the Company') }}:
                <strong>{{ $kycInfo->data['general']['company_name'] }}</strong>
            </li>
            <li class="list-group-item">
                {{ __('Date of the KYC') }}:
                <strong>{{ \Carbon\Carbon::now()->format('Y-m-d') }}</strong>
            </li>
            <li class="list-group-item">
                {{ __('Name(s) of the client(s)') }}:
                <strong>{{ $kycInfo->data['general']['company_client_1'] }}</strong>
            </li>
            @if (isset($kycInfo->data['general']['company_client_2']))
            <li class="list-group-item">
                {{ __('Name(s) of the client(s)') }}:
                <strong>{{ $kycInfo->data['general']['company_client_2'] }}</strong>
            </li>
            @endif
            @if (isset($kycInfo->data['general']['company_client_3']))
            <li class="list-group-item">
                {{ __('Name(s) of the client(s)') }}:
                <strong>{{ $kycInfo->data['general']['company_client_3'] }}</strong>
            </li>
            @endif
            <li class="list-group-item">
                {{ __('If several clients: Link between the clients?') }}:
                <strong>{{ ucwords($kycInfo->data['general']['company_clients_relation']) }}</strong>
            </li>
            <li class="list-group-item">
                {{ __('Representative(s)') }}:
                <strong>{{ $kycInfo->data['general']['company_representative_1'] }}</strong>
            </li>
            @if (isset($kycInfo->data['general']['company_representative_2']))
            <li class="list-group-item">
                {{ __('Representative(s)') }}:
                <strong>{{ $kycInfo->data['general']['company_representative_2'] }}</strong>
            </li>
            @endif
            @if (isset($kycInfo->data['general']['company_representatives_relation']))
            <li class="list-group-item">
                {{ __('If several representatives: Link between the representatives?') }}:
                <strong>{{ $kycInfo->data['general']['company_representatives_relation'] }}</strong>
            </li>
            @endif
            <li class="list-group-item">
                {{ __('Summary of the background of the relationship') }}:
                <strong>{{ $kycInfo->data['general']['summary_relation'] }}</strong>
            </li>
        @endif                        
    </ul>
    @endif
</div>
<div>
    <h4 class="step-title">{{ __('Information on assets transferred to the company') }}</h4>
    <ul class="list-group mb-4">                        
        @if (isset($kycInfo->data['asset']))
            <li class="list-group-item">
                {{ __('Range of amount of assets to be transferred during one year and currency') }}:
                @if ($kycInfo->data['asset']['company_asset_range'] == 1)
                <strong>< USD 500.000</strong>
                @elseif ($kycInfo->data['asset']['company_asset_range'] == 2)
                <strong>USD 500.000 - 1.000.000</strong>
                @elseif ($kycInfo->data['asset']['company_asset_range'] == 3)
                <strong>USD 1.000.000 - 2.500.000</strong>
                @elseif ($kycInfo->data['asset']['company_asset_range'] == 4)
                <strong>USD 2.500.000 - 7.500.000</strong>
                @elseif ($kycInfo->data['asset']['company_asset_range'] == 5)
                <strong>USD 7.500.000 - 10.000.000</strong>
                @elseif ($kycInfo->data['asset']['company_asset_range'] == 6)
                <strong>> USD 10.000.000</strong>
                @endif
            </li>
            <li class="list-group-item">
                {{ __('Requested services') }}:
                @foreach ($kycInfo->data['asset']['company_requested_service'] as $item)
                    @if ($item == 'currency_exchange')
                    <strong>{{ __('Currency exchange') }}</strong>
                    @elseif ($item == 'move_transfers')
                    <strong>{{ __('Money transfers') }}</strong>
                    @elseif ($item == 'trust_service_advistory')
                    <strong>{{ __('Trust Services Advisory') }}</strong>
                    @elseif ($item == 'crypto_exchange_and_cash_out')
                    <strong>{{ __('Crypto exchange and cash out') }}</strong>
                    @elseif ($item == 'payment_and_electronic_money_account')
                    <strong>{{ __('Payment and electronic money account') }}</strong>
                    @elseif ($item == 'forex_or_crypto_trading')
                    <strong>{{ __('Forex or Crypto Trading') }}</strong>
                    @endif
                    <span>,</span>
                @endforeach
            </li>
            <li class="list-group-item">
                <strong>{{ __('Planned frequency') }}</strong>
            </li> 
            <li class="list-group-item pl-4">
                {{ __('Number of transactions expected for one year:') }}
                <strong>{{ $kycInfo->data['asset']['transactions_expected'] }}</strong>
            </li>
            <li class="list-group-item pl-4">
                {{ __('Range of amounts of each transaction:') }}
                <strong>{{ $kycInfo->data['asset']['transaction_amount'] }}</strong>
            </li>
            <li class="list-group-item">
                {{ __('Purpose of the relationship') }}:
                @foreach ($kycInfo->data['asset']['purpose_relationship'] as $item)
                    @if ($item == 'crypto_exchange')
                    <strong>{{ __('Crypto Exchange') }}</strong>
                    @elseif ($item == 'trust_service')
                    <strong>{{ __('Trust Service') }}</strong>
                    @elseif ($item == 'trading')
                    <strong>{{ __('Trading') }}</strong>
                    @elseif ($item == 'training')
                    <strong>{{ __('Training') }}</strong>
                    @endif
                    <span>,</span>
                @endforeach
            </li>
            <li class="list-group-item">
                <strong>{{ __('Accounts from which the assets will be transferred to or by the company (if applicable)') }}</strong>
            </li>
            @if (isset($kycInfo->data['asset']['bank_name']))
            <li class="list-group-item pl-4">
                {{ __('Name of Bank:') }}
                <strong>{{ $kycInfo->data['asset']['bank_name'] }}</strong>
            </li>
            @endif
            @if (isset($kycInfo->data['asset']['bank_country']))
            <li class="list-group-item pl-4">
                {{ __('Country:') }}
                <strong>{{ getCountryNameFromCode($kycInfo->data['asset']['bank_country']) }}</strong>
            </li>
            @endif
            @if (isset($kycInfo->data['asset']['bank_account']))
            <li class="list-group-item pl-4">
                {{ __('Account number:') }}
                <strong>{{ $kycInfo->data['asset']['bank_account'] }}</strong>
            </li>
            @endif
            @if (isset($kycInfo->data['asset']['bank_swift']))
            <li class="list-group-item pl-4">
                SWIFT:
                <strong>{{ $kycInfo->data['asset']['bank_swift'] }}</strong>
            </li>
            @endif
            @if (isset($kycInfo->data['asset']['crypto_currency']))
            <li class="list-group-item pl-4">
                {{ __('Crypto Currencies used') }}:
                @if ($kycInfo->data['asset']['crypto_currency'] == 'btc')
                <strong>BTC</strong>
                @elseif ($kycInfo->data['asset']['crypto_currency'] == 'eth')
                <strong>ETH</strong>
                @elseif ($kycInfo->data['asset']['crypto_currency'] == 'usdte')
                <strong>USDT (ERC-20)</strong>
                @elseif ($kycInfo->data['asset']['crypto_currency'] == 'usdtt')
                <strong>USDT (TRC-20)</strong>
                @endif
            </li>
            @endif
            @if (isset($kycInfo->data['asset']['wallet_address']))
            <li class="list-group-item pl-4">
                {{ __('Wallet(s) from which assets will be transferred to or by the company (if applicable) and cryptocurrencies') }}:
                <strong>{{ $kycInfo->data['asset']['wallet_address'] }}</strong>
            </li>
            @endif
        @endif                        
    </ul>
</div>
<div>
    <h4 class="step-title">{{ __('Information on assets transferred to the company') }}</h4>
    <ul class="list-group mb-4">                        
        @if (isset($kycInfo->data['income']))
            <li class="list-group-item">
                <strong>{{ __('Estimated annual income') }}</strong>
            </li> 
            @if ($kycInfo->data['income']['company_income_1']['checked'] == 1)
            <li class="list-group-item">
                {{ __('Income related to work (salary)') }}:
                <strong>{{ $kycInfo->data['income']['company_income_1']['amount'] }} %</strong>
            </li>
            @endif
            @if ($kycInfo->data['income']['company_income_2']['checked'] == 1)
            <li class="list-group-item">
                {{ __('Income related to an independent activity') }}:
                <strong>{{ $kycInfo->data['income']['company_income_2']['amount'] }} %</strong>
            </li>
            @endif
            @if ($kycInfo->data['income']['company_income_3']['checked'] == 1)
            <li class="list-group-item">
                {{ __('Income related to real estate') }}:
                <strong>{{ $kycInfo->data['income']['company_income_3']['amount'] }} %</strong>
            </li>
            @endif
            @if ($kycInfo->data['income']['company_income_4']['checked'] == 1)
            <li class="list-group-item">
                {{ __('Annuity (pension, retirement, etc.)') }}:
                <strong>{{ $kycInfo->data['income']['company_income_4']['amount'] }} %</strong>
            </li>
            @endif
            @if ($kycInfo->data['income']['company_income_5']['checked'] == 1)
            <li class="list-group-item">
                {{ __('Dividends / Interests on existing investments') }}:
                <strong>{{ $kycInfo->data['income']['company_income_5']['amount'] }} %</strong>
            </li>
            @endif
            @if ($kycInfo->data['income']['company_income_6']['checked'] == 1)
            <li class="list-group-item">
                {{ __('Inheritance') }}:
                <strong>{{ $kycInfo->data['income']['company_income_6']['amount'] }} %</strong>
            </li>
            @endif
            @if ($kycInfo->data['income']['company_income_other']['checked'] == 1)
            <li class="list-group-item">
                {{ __('Other') }} ({{ $kycInfo->data['income']['company_income_other']['description'] }}):
                <strong>{{ $kycInfo->data['income']['company_income_other']['amount'] }} %</strong>
            </li>
            @endif
            <li class="list-group-item">
                {{ __('Origin of wealth') }}:
                <strong>{{ getDescriptionString($kycInfo->data['income']['origin_wealth']) }}</strong>
            </li>
            <li class="list-group-item">
                {{ __('Source of funds transferred to CSCM') }}:
                <strong>{{ getDescriptionString($kycInfo->data['income']['source_cscm']) }}</strong>
            </li>
        @endif                        
    </ul>
</div>
<div>
    <h4 class="step-title">{{ __('Personal Details') }}</h4>
    <ul class="list-group mb-4">                        
        @if (isset($kycInfo->data['personal']))
            <li class="list-group-item">
                {{ __('ID / Passport') }}:
                @if( file_exists('assets/'.$kycInfo->data['personal']['file']))
                <img class="passport" src="{{ asset($kycInfo->data['personal']['file']) }}" alt=""/>
                @endif
            </li>
            <li class="list-group-item">
                {{ __('Full Name') }}:
                <strong>{{ $kycInfo->data['personal']['first_name'] . $kycInfo->data['personal']['last_name']  }}</strong>
            </li>
            <li class="list-group-item">
                {{ __('Gender') }}:
                <strong>{{ $kycInfo->data['personal']['gender'] == 'male' ? __('Male') : (
                    $kycInfo->data['personal']['gender'] == 'female' ? __('Female') : __('Diverse')) }}</strong>
            </li>  
            <li class="list-group-item">
                {{ __('Date of Birth') }}:
                <strong>{{ $kycInfo->data['personal']['birth_date'] }}</strong>
            </li>  
            <li class="list-group-item">
                {{ __('Permanent Residential Address') }}:
                <strong>{{ $kycInfo->data['personal']['residential_address'] }}</strong>
            </li>
            <li class="list-group-item">
                {{ __('Passport Number') }}:
                <strong>{{ $kycInfo->data['personal']['passport_number'] }}</strong>
            </li>  
            <li class="list-group-item">
                <strong>{{ __('Tax Residence') }}</strong>
            </li> 
            @if ($kycInfo->data['personal']['tax_country_1'] && $kycInfo->data['personal']['tax_id_1'])
            <li class="list-group-item">
                {{ __('Country') }}: 
                <strong>{{ getCountryNameFromCode($kycInfo->data['personal']['tax_country_1']) }}</strong>
            </li> 
            <li class="list-group-item">
                {{ __('TIN or Tax ID') }}:
                <strong>{{ $kycInfo->data['personal']['tax_id_1'] }}</strong>
            </li>   
            @endif
            @if ($kycInfo->data['personal']['tax_country_2'] && $kycInfo->data['personal']['tax_id_2'])
            <li class="list-group-item">
                {{ __('Country') }}: 
                <strong>{{ getCountryNameFromCode($kycInfo->data['personal']['tax_country_2']) }}</strong>
            </li> 
            <li class="list-group-item">
                {{ __('TIN or Tax ID') }}:
                <strong>{{ $kycInfo->data['personal']['tax_id_2'] }}</strong>
            </li>   
            @endif
            @if ($kycInfo->data['personal']['tax_country_3'] && $kycInfo->data['personal']['tax_id_3'])
            <li class="list-group-item">
                {{ __('Country') }}: 
                <strong>{{ getCountryNameFromCode($kycInfo->data['personal']['tax_country_3']) }}</strong>
            </li> 
            <li class="list-group-item">
                {{ __('TIN or Tax ID') }}:
                <strong>{{ $kycInfo->data['personal']['tax_id_3'] }}</strong>
            </li>   
            @endif
        @endif                        
    </ul>
</div>

@if($kycInfo->status !== \App\Enums\KycStatus::Verified)
    <form action="{{ route('admin.kyc.action.now') }}" method="post">
        @csrf
        <input type="hidden" name="id" value="{{ $id }}">


        <div class="site-input-groups">
            <label for="" class="box-input-label">{{ __('Details Message(Optional)') }}</label>
            <textarea name="message" class="form-textarea mb-0" placeholder="Details Message"></textarea>
        </div>

        <div class="action-btns">
            <button type="submit" name="status" value="1" class="site-btn-sm primary-btn me-2">
                <i icon-name="check"></i>
                {{ __('Approve') }}
            </button>
            @if($kycInfo->status !== \App\Enums\KycStatus::Failed)
                <button type="submit" name="status" value="3" class="site-btn-sm red-btn">
                    <i icon-name="x"></i>
                    {{ __('Reject') }}
                </button>
            @endif
        </div>
    </form>
    <script>
      'use strict';
      lucide.createIcons();
    </script>
@endif
