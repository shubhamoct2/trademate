@extends('frontend::layouts.user')
@section('title')
    {{ __('KYC') }}
@endsection
@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="site-card">
                <div class="site-card-header">
                    <h3 class="title">{{ __('KYC') }}</h3>
                </div>
                <div class="site-card-body">
                    @if ($kycStatus == 'Pending')
                        <div class="site-badge warnning"> {{ __('Your Kyc Is Pending') }}</div>
                    @elseif ($kycStatus == 'Verified')
                        <div class="site-badge success"> {{ __('Your Kyc Is Verified') }} </div>
                    @else
                        <div class="row">
                            <div class="col-12">
                                <div class="reg-step-list">
                                    <div class="reg-step-item {{ $step == 0 ? 'active' : 'passed' }}">
                                        <div class="reg-step-num">1</div>
                                        <div class="reg-step-txt ml-3">{{ __('KYC Type') }}</div>
                                    </div>
                                    @if (isset($details['kyc_type']) && $details['kyc_type'] == 'company')
                                    <div class="reg-step-item {{ $step == 1 ? 'active' : ($step > 1 ? 'passed' : '')}}">
                                        <div class="reg-step-num">2</div>
                                        <div class="reg-step-txt ml-3">{{ __('General Information')}}</div>
                                    </div>
                                    <div class="reg-step-item {{ $step == 2 ? 'active' : ($step > 2 ? 'passed' : '')}}">
                                        <div class="reg-step-num">3</div>
                                        <div class="reg-step-txt ml-3">{{ __('Asset Details') }}</div>
                                    </div>
                                    <div id="stage_bank_verification" class="reg-step-item {{ $step == 3 ? 'active' : ($step > 3 ? 'passed' : '')}}">
                                        <div class="reg-step-num">4</div>
                                        <div class="reg-step-txt ml-3">{{ __('Income Details') }}</div>
                                    </div>
                                    <div id="stage_sign_agreement" class="reg-step-item {{ $step == 4 ? 'active' : ($step > 4 ? 'passed' : '')}}">
                                        <div class="reg-step-num">5</div>
                                        <div class="reg-step-txt ml-3">{{ __('Personal Details') }}</div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>                        
                        <form name="kyc_form" id="kyc_form" 
                            action="{{ route('user.kyc.submit') }}" 
                            method="post" 
                            enctype="multipart/form-data"
                        >
                            @csrf
                            <input type="hidden" name="step"  value="{{ $step }}" />
                            <div class="row">
                                <div class="col-12">
                                    <div class="progress-steps-form">                                    
                                        <div class="w-100">                                        
                                            @if ($step == 0)
                                            <div class="row">
                                                <div class="col-12">
                                                    <h2 class="step-title">{{ __('What best describes you?') }}</h2>
                                                </div>                               
                                                <div class="col-md-4 col-12">
                                                    <div class="radio-item">
                                                        <input type="radio" name="kyc_type" id="kyc_type_company" value="company" 
                                                        @if(isset($details['kyc_type']) 
                                                            && $details['kyc_type'] == "company") checked 
                                                        @endif
                                                        />
                                                        <label for="kyc_type_company">
                                                            <i icon-name="globe"></i>
                                                            <h5>{{ __('Company') }}</h5>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 col-12">
                                                    <div class="radio-item">
                                                        <input type="radio" name="kyc_type" id="kyc_type_individual" value="individual" 
                                                        @if(isset($details['kyc_type']) 
                                                            && $details['kyc_type'] == "individual") checked 
                                                        @endif
                                                        />
                                                        <label for="kyc_type_individual">
                                                            <i icon-name="user"></i>
                                                            <h5>{{ __('Private Individual') }}</h5>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 col-12">
                                                    <div class="radio-item">
                                                        <input type="radio" name="kyc_type" id="kyc_type_ubo" value="ubo" 
                                                        @if(isset($details['kyc_type']) 
                                                            && $details['kyc_type'] == "ubo") checked 
                                                        @endif
                                                        />
                                                        <label for="kyc_type_ubo">
                                                            <i icon-name="landmark"></i>
                                                            <h5>{{ __('Corporate UBO') }}</h5>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                            @if (isset($details['kyc_type']) && $details['kyc_type'] == 'company')
                                                @if ($step == 1)                                                
                                                <div class="row">
                                                    <div class="col-12">
                                                        <h2 class="step-title">{{ __('Business Relation Profile Corporate') }}</h2>
                                                    </div>
                                                    <div class="col-xl-6 col-md-12">
                                                        <label for="company_name" class="form-label">{{ __('Name of the Company') }}</label>
                                                        <div class="input-group">
                                                            <input
                                                                name="company_name"
                                                                id="company_name"
                                                                type="text"
                                                                class="form-control"                                                          
                                                                value="{{ isset($details['general']['company_name']) ? $details['general']['company_name'] : '' }}"
                                                                placeholder="{{ __('Name of the Company') }}"
                                                            />
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-6 col-md-12">
                                                        <label for="date_kyc" class="form-label">{{ __('Date of the KYC') }}</label>
                                                        <div class="input-group">
                                                            <input
                                                                name="date_kyc"
                                                                id="date_kyc"
                                                                type="text"
                                                                class="form-control"                                                                
                                                                value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"
                                                                placeholder="{{ __('Date of the KYC') }}"
                                                                disabled
                                                            />
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-6 col-md-12">
                                                        <label for="company_client_1" class="form-label">{{ __('Name(s) of the client(s)') }}</label>
                                                        <div class="input-group">
                                                            <input
                                                                name="company_client_1"
                                                                id="company_client_1"
                                                                type="text"
                                                                class="form-control"                                                                
                                                                value="{{ isset($details['general']['company_client_1']) ? $details['general']['company_client_1'] : '' }}"
                                                                placeholder="1."
                                                            />
                                                        </div>
                                                        <div class="input-group">
                                                            <input
                                                                name="company_client_2"
                                                                id="company_client_2"
                                                                type="text"
                                                                class="form-control"                                                                
                                                                value="{{ isset($details['general']['company_client_2']) ? $details['general']['company_client_2'] : '' }}"
                                                                placeholder="2."
                                                            />
                                                        </div>
                                                        <div class="input-group">
                                                            <input
                                                                name="company_client_3"
                                                                id="company_client_3"
                                                                type="text"
                                                                class="form-control"                                                                
                                                                value="{{ isset($details['general']['company_client_3']) ? $details['general']['company_client_3'] : '' }}"
                                                                placeholder="3."
                                                            />
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-6 col-md-12">
                                                        <label for="company_clients_relation" class="form-label">{{ __('If several clients: Link between the clients?') }}</label>
                                                        <div class="input-group">
                                                            <select name="company_clients_relation" id="company_clients_relation" class="nice-select site-nice-select">
                                                                <option value="">{{ __('---Select---') }}</option>
                                                                <option 
                                                                    value="family" 
                                                                    @if(isset($details['general']['company_clients_relation']) && $details['general']['company_clients_relation'] == 'family') selected @endif
                                                                >{{ __('Family') }}</option>
                                                                <option 
                                                                    value="friends"
                                                                    @if(isset($details['general']['company_clients_relation']) && $details['general']['company_clients_relation'] == 'friends') selected @endif
                                                                >{{ __('Friends') }}</option>
                                                                <option 
                                                                    value="partner"
                                                                    @if(isset($details['general']['company_clients_relation']) && $details['general']['company_clients_relation'] == 'partner') selected @endif
                                                                >{{ __('Business Partner') }}</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-6 col-md-12">
                                                        <label for="company_representative_1" class="form-label">{{ __('Representative(s)') }}</label>
                                                        <div class="input-group">
                                                            <input
                                                                name="company_representative_1"
                                                                id="company_representative_1"
                                                                type="text"
                                                                class="form-control"                                                                
                                                                value="{{ isset($details['general']['company_representative_1']) ? $details['general']['company_representative_1'] : '' }}"
                                                                placeholder="1."                                                                                                                   
                                                            />
                                                        </div>
                                                        <div class="input-group">
                                                            <input
                                                                name="company_representative_2"
                                                                id="company_representative_2"
                                                                type="text"
                                                                class="form-control"                                                                
                                                                value="{{ isset($details['general']['company_representative_2']) ? $details['general']['company_representative_2'] : '' }}"
                                                                placeholder="2."
                                                            />
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-6 col-md-12">
                                                        <label for="company_representatives_relation" class="form-label">
                                                            {{ __('If several representatives: Link between the representatives?') }}
                                                        </label>
                                                        <div class="input-group">
                                                            <select name="company_representatives_relation" id="company_representatives_relation" class="nice-select site-nice-select">
                                                                <option value="">{{ __('---Select---') }}</option>
                                                                <option 
                                                                    value="family"
                                                                    @if(isset($details['general']['company_representatives_relation']) && $details['general']['company_representatives_relation'] == 'family') selected @endif
                                                                >{{ __('Family') }}</option>
                                                                <option 
                                                                    value="friends"
                                                                    @if(isset($details['general']['company_representatives_relation']) && $details['general']['company_representatives_relation'] == 'friends') selected @endif
                                                                >{{ __('Friends') }}</option>
                                                                <option 
                                                                    value="partner"
                                                                    @if(isset($details['general']['company_representatives_relation']) && $details['general']['company_representatives_relation'] == 'partner') selected @endif
                                                                >{{ __('Business Partner') }}</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-6 col-md-12">
                                                        <label for="summary_relation" class="form-label">{{ __('Summary of the background of the relationship') }}</label>
                                                        <div class="input-group">
                                                            <input
                                                                name="summary_relation"
                                                                id="summary_relation"
                                                                type="text"
                                                                class="form-control"                                                                
                                                                value="{{ isset($details['general']['summary_relation']) ? $details['general']['summary_relation'] : '' }}"
                                                                placeholder="{{ __('Personal Recommendation') }}"                                                                
                                                            />
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                                @if ($step == 2)                                
                                                <div class="row">
                                                    <div class="col-12">
                                                        <h2 class="step-title">{{ __('Information on assets transferred to the company') }}</h2>
                                                    </div>
                                                    <div class="col-xl-6 col-md-12">
                                                        <label for="company_asset_range" class="form-label">{{ __('Range of amount of assets to be transferred during one year and currency') }}</label>
                                                        <div class="input-group">
                                                            <select name="company_asset_range" id="company_asset_range" class="nice-select site-nice-select">
                                                                <option value="">{{ __('---Select---') }}</option>
                                                                <option value="1" @if(isset($details['asset']['company_asset_range']) && $details['asset']['company_asset_range'] == 1) selected @endif
                                                                >< CHF 500.000</option>
                                                                <option value="2" @if(isset($details['asset']['company_asset_range']) && $details['asset']['company_asset_range'] == 2) selected @endif
                                                                >CHF 500.000 - 1.000.000</option>
                                                                <option value="3" @if(isset($details['asset']['company_asset_range']) && $details['asset']['company_asset_range'] == 3) selected @endif
                                                                >CHF 1.000.000 - 2.500.000</option>
                                                                <option value="4" @if(isset($details['asset']['company_asset_range']) && $details['asset']['company_asset_range'] == 4) selected @endif
                                                                >CHF 2.500.000 - 7.500.000</option>
                                                                <option value="5" @if(isset($details['asset']['company_asset_range']) && $details['asset']['company_asset_range'] == 5) selected @endif
                                                                >CHF 7.500.000 - 10.000.000</option>
                                                                <option value="6" @if(isset($details['asset']['company_asset_range']) && $details['asset']['company_asset_range'] == 6) selected @endif
                                                                >> CHF 10.000.000</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-6 col-md-12">
                                                        <label for="company_requested_service" class="form-label">{{ __('Requested services') }}</label>
                                                        <div class="input-group">
                                                            <select 
                                                                name="company_requested_service[]" 
                                                                id="company_requested_service" 
                                                                class="multiple-select"
                                                                multiple="multiple"
                                                            >
                                                                <option 
                                                                    value="currency_exchange"
                                                                    @if(isset($details['asset']['company_requested_service']) && in_array('currency_exchange', $details['asset']['company_requested_service'])) selected @endif
                                                                >{{ __('Currency exchange') }}</option>
                                                                <option 
                                                                    value="move_transfers"
                                                                    @if(isset($details['asset']['company_requested_service']) && in_array('move_transfers', $details['asset']['company_requested_service'])) selected @endif
                                                                >{{ __('Money transfers') }}</option>
                                                                <option 
                                                                    value="trust_service_advistory"
                                                                    @if(isset($details['asset']['company_requested_service']) && in_array('trust_service_advistory', $details['asset']['company_requested_service'])) selected @endif
                                                                >{{ __('Trust Services Advisory') }}</option>
                                                                <option 
                                                                    value="crypto_exchange_and_cash_out"
                                                                    @if(isset($details['asset']['company_requested_service']) && in_array('crypto_exchange_and_cash_out', $details['asset']['company_requested_service'])) selected @endif
                                                                >{{ __('Crypto exchange and cash out') }}</option>
                                                                <option 
                                                                    value="payment_and_electronic_money_account"
                                                                    @if(isset($details['asset']['company_requested_service']) && in_array('payment_and_electronic_money_account', $details['asset']['company_requested_service'])) selected @endif
                                                                >{{ __('Payment and electronic money account') }}</option>
                                                                <option 
                                                                    value="forex_or_crypto_trading"
                                                                    @if(isset($details['asset']['company_requested_service']) && in_array('forex_or_crypto_trading', $details['asset']['company_requested_service'])) selected @endif
                                                                >{{ __('Forex or Crypto Trading') }}</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-12 col-md-12">
                                                        <label class="input-group-label">{{ __('Planned frequency') }}</label>
                                                    </div>
                                                    <div class="col-xl-6 col-md-12">
                                                        <label for="transactions_expected" class="form-label">{{ __('Number of transactions expected for one year:') }}</label>
                                                        <div class="input-group">
                                                            <input
                                                                id="transactions_expected"
                                                                type="number"
                                                                class="form-control"
                                                                name="transactions_expected"
                                                                value="{{ isset($details['asset']['transactions_expected']) ? $details['asset']['transactions_expected'] : '' }}"
                                                                placeholder=""                                                                
                                                            />
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-6 col-md-12">
                                                        <label for="transaction_amount" class="form-label">{{ __('Range of amounts of each transaction:') }}</label>
                                                        <div class="input-group">
                                                            <input
                                                                id="transaction_amount"
                                                                type="number"
                                                                class="form-control"
                                                                name="transaction_amount"
                                                                value="{{ isset($details['asset']['transaction_amount']) ? $details['asset']['transaction_amount'] : '' }}"
                                                                placeholder=""                                                                
                                                            />
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-6 col-md-12">
                                                        <label for="purpose_relationship" class="form-label">{{ __('Purpose of the relationship') }}</label>
                                                        <div class="input-group">
                                                            <select 
                                                                name="purpose_relationship[]" 
                                                                id="purpose_relationship" 
                                                                class="multiple-select"
                                                                multiple="multiple"
                                                            >
                                                                <option 
                                                                    value="crypto_exchange"
                                                                    @if(isset($details['asset']['purpose_relationship']) && in_array('crypto_exchange', $details['asset']['purpose_relationship'])) selected @endif
                                                                >{{ __('Crypto Exchange') }}</option>
                                                                <option 
                                                                    value="trust_service"
                                                                    @if(isset($details['asset']['purpose_relationship']) && in_array('trust_service', $details['asset']['purpose_relationship'])) selected @endif
                                                                >{{ __('Trust Service') }}</option>
                                                                <option 
                                                                    value="trading"
                                                                    @if(isset($details['asset']['purpose_relationship']) && in_array('trading', $details['asset']['purpose_relationship'])) selected @endif
                                                                >{{ __('Trading') }}</option>
                                                                <option 
                                                                    value="training"
                                                                    @if(isset($details['asset']['purpose_relationship']) && in_array('training', $details['asset']['purpose_relationship'])) selected @endif
                                                                >{{ __('Training') }}</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-12 col-md-12">
                                                        <label class="input-group-label">{{ __('Accounts from which the assets will be transferred to or by the company (if applicable)') }}</label>
                                                    </div>
                                                    <div class="col-xl-6 col-md-12">
                                                        <label for="bank_name" class="form-label">{{ __('Name of Bank:') }}</label>
                                                        <div class="input-group">
                                                            <input
                                                                id="bank_name"
                                                                type="text"
                                                                class="form-control"
                                                                name="bank_name"
                                                                value="{{ isset($details['asset']['bank_name']) ? $details['asset']['bank_name'] : '' }}"
                                                                placeholder=""                                                                
                                                            />
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-6 col-md-12">
                                                        <label for="bank_country" class="form-label">{{ __('Country:') }}</label>
                                                        <div class="input-group">
                                                            <select name="bank_country" id="bank_country" class="nice-select site-nice-select">
                                                                <option value="">{{ __('---Select---') }}</option>
                                                                @foreach( getCountries() as $country)
                                                                <option 
                                                                    @if(isset($details['asset']['bank_country']) && $details['asset']['bank_country'] == $country['code']) selected @endif
                                                                    value="{{ $country['code'] }}"
                                                                >
                                                                    {{ $country['name']  }}
                                                                </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-6 col-md-12">
                                                        <label for="bank_account" class="form-label">{{ __('Account number:') }}</label>
                                                        <div class="input-group">
                                                            <input
                                                                id="bank_account"
                                                                type="number"
                                                                class="form-control"
                                                                name="bank_account"
                                                                value="{{ isset($details['asset']['bank_account']) ? $details['asset']['bank_account'] : '' }}"
                                                                placeholder=""                                                                
                                                            />
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-6 col-md-12">
                                                        <label for="bank_swift" class="form-label">SWIFT:</label>
                                                        <div class="input-group">
                                                            <input
                                                                id="bank_swift"
                                                                type="number"
                                                                class="form-control"
                                                                name="bank_swift"
                                                                value="{{ isset($details['asset']['bank_swift']) ? $details['asset']['bank_swift'] : '' }}"
                                                                placeholder=""                                                                
                                                            />
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-6 col-md-12">
                                                        <label for="crypto_currency" class="form-label">{{ __('Crypto Currencies used') }}</label>
                                                        <div class="input-group">
                                                            <select 
                                                                name="crypto_currency" 
                                                                id="crypto_currency" 
                                                                class="nice-select site-nice-select"
                                                            >
                                                                <option value="">{{ __('---Select---') }}</option>
                                                                <option 
                                                                    value="btc" 
                                                                    @if(isset($details['asset']['crypto_currency']) && $details['asset']['crypto_currency'] == 'btc') selected @endif
                                                                >BTC</option>
                                                                <option 
                                                                    value="eth"
                                                                    @if(isset($details['asset']['crypto_currency']) && $details['asset']['crypto_currency'] == 'eth') selected @endif
                                                                >ETH</option>
                                                                <option 
                                                                    value="usdte"
                                                                    @if(isset($details['asset']['crypto_currency']) && $details['asset']['crypto_currency'] == 'usdte') selected @endif
                                                                >USDT(ERC-20)</option>
                                                                <option 
                                                                    value="usdtt"
                                                                    @if(isset($details['asset']['crypto_currency']) && $details['asset']['crypto_currency'] == 'usdtt') selected @endif
                                                                >USDT(TRC-20)</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-6 col-md-12">
                                                        <label for="wallet_address" class="form-label">{{ __('Wallet(s) from which assets will be transferred to or by the company (if applicable) and cryptocurrencies') }}</label>
                                                        <div class="input-group">
                                                            <input
                                                                id="wallet_address"
                                                                type="text"
                                                                class="form-control"
                                                                name="wallet_address"
                                                                value="{{ isset($details['asset']['wallet_address']) ? $details['asset']['wallet_address'] : '' }}"
                                                                placeholder="{{ __('Please copy your Wallet Address here:') }}"                                                                
                                                            />
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                                @if ($step == 3)                                                
                                                <div class="row">
                                                    <div class="col-12">
                                                        <h2 class="step-title">{{ __('Information on assets transferred to the company') }}</h2>
                                                    </div>
                                                    <div class="col-xl-12 col-md-12">
                                                        <label class="input-group-label">{{ __('Estimated annual income') }}</label>
                                                    </div>
                                                    <div class="col-xl-6 col-md-12">
                                                        <div>
                                                            <input
                                                                class="form-check-input check-input"
                                                                type="checkbox"
                                                                name="company_income_1"
                                                                id="company_income_1"
                                                                @if(isset($details['income']['company_income_1']) 
                                                                    && $details['income']['company_income_1']['checked']) checked 
                                                                @endif
                                                            />
                                                            <label class="form-check-label" for="company_income_1">
                                                                {{ __('Income related to work (salary)') }}
                                                            </label>
                                                        </div>  
                                                        <div class="input-group percent-input">
                                                            <input
                                                                name="company_income_1_amount"
                                                                id="company_income_1_amount"
                                                                type="number"
                                                                class="form-control"                                                                
                                                                value="{{ isset($details['income']['company_income_1']) ? $details['income']['company_income_1']['amount'] : '' }}"
                                                                placeholder="0"                                                                
                                                            />
                                                        </div>                                                      
                                                    </div>
                                                    <div class="col-xl-6 col-md-12">
                                                        <div>
                                                            <input
                                                                class="form-check-input check-input"
                                                                type="checkbox"
                                                                name="company_income_2"
                                                                id="company_income_2"
                                                                @if(isset($details['income']['company_income_2']) 
                                                                    && $details['income']['company_income_2']['checked']) checked 
                                                                @endif
                                                            />
                                                            <label class="form-check-label" for="company_income_2">
                                                                {{ __('Income related to an independent activity') }}
                                                            </label>
                                                        </div>  
                                                        <div class="input-group percent-input">
                                                            <input
                                                                name="company_income_2_amount"
                                                                id="company_income_2_amount"
                                                                type="number"
                                                                class="form-control"                                                                
                                                                value="{{ isset($details['income']['company_income_2']) ? $details['income']['company_income_2']['amount'] : '' }}"
                                                                placeholder="0"
                                                            />
                                                        </div>                                                      
                                                    </div>
                                                    <div class="col-xl-6 col-md-12">
                                                        <div>
                                                            <input
                                                                class="form-check-input check-input"
                                                                type="checkbox"
                                                                name="company_income_3"
                                                                id="company_income_3"
                                                                @if(isset($details['income']['company_income_3']) 
                                                                    && $details['income']['company_income_3']['checked']) checked 
                                                                @endif
                                                            />
                                                            <label class="form-check-label" for="company_income_3">
                                                                {{ __('Income related to real estate') }}
                                                            </label>
                                                        </div>  
                                                        <div class="input-group percent-input">
                                                            <input
                                                                name="company_income_3_amount"
                                                                id="company_income_3_amount"
                                                                type="number"
                                                                class="form-control"                                                                
                                                                value="{{ isset($details['income']['company_income_3']) ? $details['income']['company_income_3']['amount'] : '' }}"
                                                                placeholder="0"
                                                            />
                                                        </div>                                                      
                                                    </div>
                                                    <div class="col-xl-6 col-md-12">
                                                        <div>
                                                            <input
                                                                class="form-check-input check-input"
                                                                type="checkbox"
                                                                name="company_income_4"
                                                                id="company_income_4"
                                                                @if(isset($details['income']['company_income_4']) 
                                                                    && $details['income']['company_income_4']['checked']) checked 
                                                                @endif
                                                            />
                                                            <label class="form-check-label" for="company_income_4">
                                                                {{ __('Annuity (pension, retirement, etc.)') }}
                                                            </label>
                                                        </div>  
                                                        <div class="input-group percent-input">
                                                            <input
                                                                name="company_income_4_amount"
                                                                id="company_income_4_amount"
                                                                type="number"
                                                                class="form-control"                                                                
                                                                value="{{ isset($details['income']['company_income_4']) ? $details['income']['company_income_4']['amount'] : '' }}"
                                                                placeholder="0"
                                                            />
                                                        </div>                                                      
                                                    </div>
                                                    <div class="col-xl-6 col-md-12">
                                                        <div>
                                                            <input
                                                                class="form-check-input check-input"
                                                                type="checkbox"
                                                                name="company_income_5"
                                                                id="company_income_5"
                                                                @if(isset($details['income']['company_income_5']) 
                                                                    && $details['income']['company_income_5']['checked']) checked 
                                                                @endif
                                                            />
                                                            <label class="form-check-label" for="company_income_5">
                                                                {{ __('Dividends / Interests on existing investments') }}
                                                            </label>
                                                        </div>  
                                                        <div class="input-group percent-input">
                                                            <input
                                                                name="company_income_5_amount"
                                                                id="company_income_5_amount"
                                                                type="number"
                                                                class="form-control"                                                                
                                                                value="{{ isset($details['income']['company_income_5']) ? $details['income']['company_income_5']['amount'] : '' }}"
                                                                placeholder="0"
                                                            />
                                                        </div>                                                      
                                                    </div>
                                                    <div class="col-xl-6 col-md-12">
                                                        <div>
                                                            <input
                                                                class="form-check-input check-input"
                                                                type="checkbox"
                                                                name="company_income_6"
                                                                id="company_income_6"
                                                                @if(isset($details['income']['company_income_6']) 
                                                                    && $details['income']['company_income_6']['checked']) checked 
                                                                @endif
                                                            />
                                                            <label class="form-check-label" for="company_income_6">
                                                                {{ __('Inheritance') }}
                                                            </label>
                                                        </div>  
                                                        <div class="input-group percent-input">
                                                            <input
                                                                name="company_income_6_amount"
                                                                id="company_income_6_amount"
                                                                type="number"
                                                                class="form-control"                                                                
                                                                value="{{ isset($details['income']['company_income_6']) ? $details['income']['company_income_6']['amount'] : '' }}"
                                                                placeholder="0"
                                                            />
                                                        </div>                                                      
                                                    </div>
                                                    <div class="col-xl-6 col-md-12">
                                                        <div>
                                                            <input
                                                                class="form-check-input check-input"
                                                                type="checkbox"
                                                                name="company_income_other"
                                                                id="company_income_other"
                                                                @if(isset($details['income']['company_income_other']) 
                                                                    && $details['income']['company_income_other']['checked']) checked 
                                                                @endif
                                                            />
                                                            <label class="form-check-label" for="company_income_other">
                                                                {{ __('Other (please specify)') }}
                                                            </label>
                                                        </div>  
                                                        <div class="input-group">
                                                            <input
                                                                name="company_income_other_description"
                                                                id="company_income_other_description"
                                                                type="text"
                                                                class="form-control"                                                                
                                                                value="{{ isset($details['income']['company_income_other']) ? $details['income']['company_income_other']['description'] : '' }}"
                                                                placeholder="{{ __('Please specify)') }}"
                                                            />
                                                        </div>   
                                                        <div class="input-group percent-input">
                                                            <input
                                                                name="company_income_other_amount"
                                                                id="company_income_other_amount"
                                                                type="number"
                                                                class="form-control"                                                                
                                                                value="{{ isset($details['income']['company_income_other']) ? $details['income']['company_income_other']['amount'] : '' }}"
                                                                placeholder="0"
                                                            />
                                                        </div>                                                      
                                                    </div>
                                                    <div class="col-xl-12 col-md-12">
                                                        <label class="input-group-label"></label>
                                                    </div>
                                                    <div class="col-xl-6 col-md-12">
                                                        <label for="origin_wealth" class="form-label">
                                                            {{ __('Origin of wealth') }}
                                                        </label>
                                                        <div class="input-group">
                                                            <input
                                                                id="origin_wealth"
                                                                name="origin_wealth"
                                                                type="text"
                                                                class="form-control"                                                                
                                                                value="{{ isset($details['income']['origin_wealth']) ? $details['income']['origin_wealth'] : '' }}"
                                                                placeholder=""                                                                
                                                            />
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-6 col-md-12">
                                                        <label for="source_cscm" class="form-label">
                                                            {{ __('Source of funds transferred to CSCM') }}
                                                        </label>
                                                        <div class="input-group">
                                                            <input
                                                                id="source_cscm"
                                                                name="source_cscm"
                                                                type="text"
                                                                class="form-control"                                                                
                                                                value="{{ isset($details['income']['source_cscm']) ? $details['income']['source_cscm'] : '' }}"
                                                                placeholder=""                                                                
                                                            />
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                                @if ($step == 4)
                                                <div class="row">
                                                    <div class="col-12">
                                                        <h2 class="step-title">{{ __('Personal Details and Tax Residence') }}</h2>
                                                    </div>
                                                    <div class="col-xl-12 col-md-12">
                                                        <label class="input-group-label">{{ __('Personal Details') }}</label>
                                                    </div>
                                                    <div class="col-xl-12 col-md-12">
                                                        <div class="body-title">{{ __('ID / Passport') }}</div>
                                                        <div class="wrap-custom-file">
                                                            <input
                                                                type="file"
                                                                name="kyc_credential_file"
                                                                id="kyc_credential_file"
                                                            />
                                                            <label for="kyc_credential_file">
                                                                <img
                                                                    class="upload-icon"
                                                                    src="{{ asset('global/materials/upload.svg') }}"
                                                                    alt=""
                                                                />
                                                                <span>{{ __('Select '). __('ID / Passport') }}</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-6 col-md-12">
                                                        <label for="first_name" class="form-label">{{ __('First Name') }}</label>
                                                        <div class="input-group">
                                                            <input
                                                                name="first_name"
                                                                id="first_name"
                                                                type="text"
                                                                class="form-control"                                                          
                                                                value="{{ isset($details['personal']['first_name']) ? $details['personal']['first_name'] : '' }}"
                                                                placeholder="{{ __('First Name') }}"
                                                            />
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-6 col-md-12">
                                                        <label for="last_name" class="form-label">{{ __('Last Name') }}</label>
                                                        <div class="input-group">
                                                            <input
                                                                name="last_name"
                                                                id="last_name"
                                                                type="text"
                                                                class="form-control"                                                          
                                                                value="{{ isset($details['personal']['last_name']) ? $details['personal']['last_name'] : '' }}"
                                                                placeholder="{{ __('Last Name') }}"
                                                            />
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-6 col-md-12">
                                                        <label for="birth_date" class="form-label">{{ __('Date of Birth') }}</label>
                                                        <div class="input-group">
                                                            <input
                                                                name="birth_date"
                                                                id="birth_date"
                                                                type="date"
                                                                class="form-control"                                                          
                                                                value="{{ isset($details['personal']['birth_date']) ? $details['personal']['birth_date'] : '' }}"
                                                                placeholder="{{ __('Date of Birth') }}"
                                                            />
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-6 col-md-12">
                                                        <label for="gender" class="form-label">{{ __('Gender') }}</label>
                                                        <div class="input-group">
                                                            <select name="gender" id="gender" class="nice-select site-nice-select">
                                                                <option value="">{{ __('---Select---') }}</option>
                                                                <option 
                                                                    value="male" 
                                                                    @if(isset($details['personal']['gender']) && 
                                                                        $details['personal']['gender'] == 'male') selected @endif
                                                                >{{ __('Male') }}</option>
                                                                <option 
                                                                    value="female" 
                                                                    @if(isset($details['personal']['gender']) && 
                                                                        $details['personal']['gender'] == 'male') selected @endif
                                                                >{{ __('Feale') }}</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-6 col-md-12">
                                                        <label for="residential_address" class="form-label">{{ __('Permanent Residential Address') }}</label>
                                                        <div class="input-group">
                                                            <input
                                                                name="residential_address"
                                                                id="residential_address"
                                                                type="text"
                                                                class="form-control"                                                          
                                                                value="{{ isset($details['personal']['residential_address']) ? $details['personal']['residential_address'] : '' }}"
                                                                placeholder="{{ __('Address') }}"
                                                            />
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-6 col-md-12">
                                                        <label for="passport_number" class="form-label">{{ __('Passport Number') }}</label>
                                                        <div class="input-group">
                                                            <input
                                                                name="passport_number"
                                                                id="passport_number"
                                                                type="text"
                                                                class="form-control"                                                          
                                                                value="{{ isset($details['personal']['passport_number']) ? $details['personal']['passport_number'] : '' }}"
                                                                placeholder=""
                                                            />
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-12 col-md-12">
                                                        <label class="input-group-label">{{ __('Tax Residence') }}</label>
                                                    </div>
                                                    <div class="col-xl-6 col-md-12">
                                                        <label for="tax_country_1" class="form-label">{{ __('Country') }}</label>
                                                        <div class="input-group">
                                                            <select name="tax_country_1" id="tax_country_1" class="nice-select site-nice-select">
                                                                <option value="">{{ __('---Select---') }}</option>
                                                                @foreach( getCountries() as $country)
                                                                <option 
                                                                    @if(isset($details['personal']['tax_country_1']) && $details['personal']['tax_country_1'] == $country['code']) selected @endif
                                                                    value="{{ $country['code'] }}"
                                                                >
                                                                    {{ $country['name']  }}
                                                                </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-6 col-md-12">
                                                        <label for="tax_id_1" class="form-label">{{ __('TIN or Tax ID') }}</label>
                                                        <div class="input-group">
                                                            <input
                                                                name="tax_id_1"
                                                                id="tax_id_1"
                                                                type="number"
                                                                class="form-control"
                                                                value="{{ isset($details['personal']['tax_id_1']) ? $details['personal']['tax_id_1'] : '' }}"
                                                                placeholder=""                                                                
                                                            />
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-6 col-md-12">
                                                        <label for="tax_country_2" class="form-label">{{ __('Country') }}</label>
                                                        <div class="input-group">
                                                            <select name="tax_country_2" id="tax_country_2" class="nice-select site-nice-select">
                                                                <option value="">{{ __('---Select---') }}</option>
                                                                @foreach( getCountries() as $country)
                                                                <option 
                                                                    @if(isset($details['personal']['tax_country_2']) && $details['personal']['tax_country_2'] == $country['code']) selected @endif
                                                                    value="{{ $country['code'] }}"
                                                                >
                                                                    {{ $country['name']  }}
                                                                </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-6 col-md-12">
                                                        <label for="tax_id_2" class="form-label">{{ __('TIN or Tax ID') }}</label>
                                                        <div class="input-group">
                                                            <input
                                                                name="tax_id_2"
                                                                id="tax_id_2"
                                                                type="number"
                                                                class="form-control"
                                                                value="{{ isset($details['personal']['tax_id_2']) ? $details['personal']['tax_id_2'] : '' }}"
                                                                placeholder=""                                                                
                                                            />
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-6 col-md-12">
                                                        <label for="tax_country_3" class="form-label">{{ __('Country') }}</label>
                                                        <div class="input-group">
                                                            <select name="tax_country_3" id="tax_country_3" class="nice-select site-nice-select">
                                                                <option value="">{{ __('---Select---') }}</option>
                                                                @foreach( getCountries() as $country)
                                                                <option 
                                                                    @if(isset($details['personal']['tax_country_3']) && $details['personal']['tax_country_3'] == $country['code']) selected @endif
                                                                    value="{{ $country['code'] }}"
                                                                >
                                                                    {{ $country['name']  }}
                                                                </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-6 col-md-12">
                                                        <label for="tax_id_3" class="form-label">{{ __('TIN or Tax ID') }}</label>
                                                        <div class="input-group">
                                                            <input
                                                                name="tax_id_3"
                                                                id="tax_id_3"
                                                                type="number"
                                                                class="form-control"
                                                                value="{{ isset($details['personal']['tax_id_3']) ? $details['personal']['tax_id_3'] : '' }}"
                                                                placeholder=""                                                                
                                                            />
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-6 col-md-12">
                                                        <div>
                                                            <input
                                                                class="form-check-input check-input"
                                                                type="checkbox"
                                                                name="tax_other"
                                                                id="tax_other"
                                                                @if(isset($details['personal']['tax_other']) 
                                                                    && $details['personal']['tax_other']['checked']) checked 
                                                                @endif
                                                            />
                                                            <label class="form-check-label" for="tax_other">
                                                                {{ __('If no TIN or Tax ID has been supplied, tick this box') }}
                                                            </label>
                                                        </div>  
                                                        <div class="input-group">
                                                            <input
                                                                name="tax_other_reason"
                                                                id="tax_other_reason"
                                                                type="text"
                                                                class="form-control"                                                                
                                                                value="{{ isset($details['personal']['tax_other']) ? 
                                                                    $details['personal']['tax_other']['description'] : '' }}"
                                                                placeholder="{{ __('Other reasons)') }}"
                                                            />
                                                        </div>                                                   
                                                    </div>
                                                </div>
                                                @endif
                                            @endif
                                        </div>
                                        <div class="field-step-btn">
                                            @if ($step >= 1)
                                            <a href="{{ route('user.kyc.back') }}" class="site-btn green-btn">{{ __('Back') }}</a>
                                            @endif
                                            @if ($step <= $max_step)
                                            <button type="submit" class="site-btn green-btn">
                                                {{ $step == $max_step ? __('Complete') : __('Next') }}
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>                                           
                        </form>
                        
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script type="text/javascript">
        /*$("form").submit(function(e) {
            e.preventDefault();
            var v= $(this).serialize();
            console.log(v);                                        
        });*/
    </script>

    @if ($step >= 1)
    <script type="text/javascript">
        $(document).ready(function() {
            if ($('.multiple-select')) {
                $('select[multiple]').multiselect({});
            }
        });
    </script>
    @endif
    @if ((isset($details['kyc_type']) && $details['kyc_type'] == "company"))
        @if ($step == 1)
        <script type="text/javascript">
            $(document).ready(function() {
                jQuery.validator.addMethod('selectcheck_company_clients_relation', function (value) {
                    if ($('#company_client_2').value != '' || $('#company_client_3').value != '') {
                        return (value != '');
                    }  else {
                        return true;
                    }
                }, "Value required");

                jQuery.validator.addMethod('selectcheck_company_representatives_relation', function (value) {
                    if ($('#company_representative_2').value != '') {
                        return (value != '');
                    }  else {
                        return true;
                    }
                }, "Value required");

                $('#kyc_form').validate({ 
                    ignore: [],
                    rules: {
                        company_name: {
                            required: true
                        },
                        date_kyc: {
                            required: true
                        },
                        company_client_1: {
                            required: true
                        },
                        company_clients_relation: {
                            // selectcheck_company_clients_relation: true
                        },
                        company_representative_1: {
                            required: true
                        },
                        company_representatives_relation: {
                            // selectcheck_company_representatives_relation: true
                        },
                        summary_relation: {
                            required: true
                        }
                    },
                    errorPlacement: function(error, element) {
                        error.insertAfter(element);           
                    },
                    submitHandler: function(form) {
                        form.submit();
                    }
                });
            });
        </script> 
        @endif 
        @if ($step == 2)
        <script type="text/javascript">
            $(document).ready(function() {
                $('#kyc_form').validate({ 
                    ignore: [],
                    rules: {
                        company_requested_service: {
                            required: true
                        },
                        transactions_expected: {
                            required: true
                        },
                        transaction_amount: {
                            required: true
                        },
                        purpose_relationship: {
                            required: true
                        },
                        bank_name: {
                            required: true
                        },
                        bank_account: {
                            required: true
                        },
                        bank_swift: {
                            required: true
                        },
                        wallet_address: {
                            required: true
                        },
                    },
                    errorPlacement: function(error, element) {
                        error.insertAfter(element);           
                    },
                    submitHandler: function(form) {
                        form.submit();
                    }
                });
            });
        </script> 
        @endif
        @if ($step == 3)
        <script type="text/javascript">
            $(document).ready(function() {
                $.validator.addMethod('required_if_checked', function (value, element) {
                    var checkbox_id = element.id.substr(0, element.id.length - 7);
                    if ($('#' + checkbox_id).prop('checked')) {
                        if (value === 0 || value === '') {
                            return false;
                        }
                    }
                    return true;
                }, "This field is required.");

                $.validator.addMethod('percentage_check', function (value, element) {
                    var checkbox_id = element.id.substr(0, element.id.length - 7);
                    if ($('#' + checkbox_id).prop('checked')) {
                        var percentage = parseFloat(value);
                        if (isNaN(percentage) || percentage < 0 || percentage > 100) {
                            return false;
                        }
                    }
                    return true;                    
                }, "Invalid percentage value.");
                
                $.validator.addMethod('required_desc_if_checked', function (value, element) {
                    var checkbox_id = 'company_income_other';
                    if ($('#' + checkbox_id).prop('checked')) {
                        if (value === '') {
                            return false;
                        }
                    }
                    return true;
                }, "This field is required.");

                $('#kyc_form').validate({ 
                    ignore: [],
                    rules: {
                        company_income_1_amount: {
                            required_if_checked: true,
                            percentage_check: true,
                        },
                        company_income_2_amount: {
                            required_if_checked: true,
                            percentage_check: true,
                        },
                        company_income_3_amount: {
                            required_if_checked: true,
                            percentage_check: true,
                        },
                        company_income_4_amount: {
                            required_if_checked: true,
                            percentage_check: true,
                        },
                        company_income_5_amount: {
                            required_if_checked: true,
                            percentage_check: true,
                        },
                        company_income_6_amount: {
                            required_if_checked: true,
                            percentage_check: true,
                        },
                        company_income_other_amount: {
                            required_if_checked: true,
                            percentage_check: true,
                        },
                        company_income_other_description: {
                            required_desc_if_checked: true,                            
                        },
                        origin_wealth: {
                            required: true,
                        },
                        source_cscm: {
                            required: true,
                        }
                    },
                    errorPlacement: function(error, element) {
                        error.insertAfter(element);       
                    },
                    submitHandler: function(form) {
                        form.submit();
                    }
                });
            });
        </script> 
        @endif
        @if ($step == 4)
        <script type="text/javascript">
            $(document).ready(function() {
                $.validator.addMethod('required_if_nonchecked', function (value, element) {
                    if (!$('#tax_other').prop('checked')) {
                        if (value === 0 || value === '') {
                            return false;
                        }
                    }
                    return true;
                }, "This field is required.");

                $('#kyc_form').validate({ 
                    ignore: [],
                    rules: {
                        kyc_credential_file: {
                            required: true,
                            accept: 'image/*'
                        },
                        first_name: {
                            required: true
                        },
                        last_name: {
                            required: true
                        },
                        birth_date: {
                            required: true
                        },
                        residential_address: {
                            required: true
                        },
                        passport_number: {
                            required: true
                        },
                        tax_id_1: {
                            required_if_nonchecked: true
                        },
                    },
                    errorPlacement: function(error, element) {
                        if (element.attr('id') === 'kyc_credential_file') {
                            error.insertAfter(element.parent());           
                        } else {
                            error.insertAfter(element);           
                        }
                        
                    },
                    submitHandler: function(form) {
                        form.submit();
                    }
                });
            });
        </script> 
        @endif
    @endif
@endsection
