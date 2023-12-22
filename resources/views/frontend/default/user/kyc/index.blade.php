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
                                        <div class="reg-step-txt ml-3">{{ __('Last Details') }}</div>
                                    </div>
                                    <div id="stage_sign_agreement" class="reg-step-item {{ $step == 4 ? 'active' : ($step > 4 ? 'passed' : '')}}">
                                        <div class="reg-step-num">5</div>
                                        <div class="reg-step-txt ml-3">Sign Agreement</div>
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
                                            <input type="hidden" name="direction" id="step_direction" value="1" />
                                            @if ($step == 0)
                                            <div class="row">
                                                <div class="col-12">
                                                    <h2 class="step-title">{{ __('What best describes you?') }}</h2>
                                                </div>                               
                                                <div class="col-md-4 col-12">
                                                    <div class="radio-item">
                                                        <input type="radio" name="kyc_type" id="kyc_type_company" value="company" />
                                                        <label for="kyc_type_company">
                                                            <i icon-name="globe"></i>
                                                            <h5>{{ __('Company') }}</h5>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 col-12">
                                                    <div class="radio-item">
                                                        <input type="radio" name="kyc_type" id="kyc_type_individual" value="individual" />
                                                        <label for="kyc_type_individual">
                                                            <i icon-name="user"></i>
                                                            <h5>{{ __('Private Individual') }}</h5>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 col-12">
                                                    <div class="radio-item">
                                                        <input type="radio" name="kyc_type" id="kyc_type_ubo" value="ubo" />
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
                                                                value="{{ isset($details['company_name']) ? $details['company_name'] : '' }}"
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
                                                                value="{{ isset($details['company_client_1']) ? $details['company_client_1'] : '' }}"
                                                                placeholder="1."
                                                            />
                                                        </div>
                                                        <div class="input-group">
                                                            <input
                                                                name="company_client_2"
                                                                id="company_client_2"
                                                                type="text"
                                                                class="form-control"                                                                
                                                                value="{{ isset($details['company_client_2']) ? $details['company_client_2'] : '' }}"
                                                                placeholder="2."
                                                            />
                                                        </div>
                                                        <div class="input-group">
                                                            <input
                                                                name="company_client_3"
                                                                id="company_client_3"
                                                                type="text"
                                                                class="form-control"                                                                
                                                                value="{{ isset($details['company_client_3']) ? $details['company_client_3'] : '' }}"
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
                                                                    @if(isset($details['company_clients_relation']) && $details['company_clients_relation'] == 'family') selected @endif
                                                                >{{ __('Family') }}</option>
                                                                <option 
                                                                    value="friends"
                                                                    @if(isset($details['company_clients_relation']) && $details['company_clients_relation'] == 'friends') selected @endif
                                                                >{{ __('Friends') }}</option>
                                                                <option 
                                                                    value="partner"
                                                                    @if(isset($details['company_clients_relation']) && $details['company_clients_relation'] == 'partner') selected @endif
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
                                                                value="{{ isset($details['company_representative_1']) ? $details['company_representative_1'] : '' }}"
                                                                placeholder="1."                                                                                                                   
                                                            />
                                                        </div>
                                                        <div class="input-group">
                                                            <input
                                                                name="company_representative_2"
                                                                id="company_representative_2"
                                                                type="text"
                                                                class="form-control"                                                                
                                                                value="{{ isset($details['company_representative_2']) ? $details['company_representative_2'] : '' }}"
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
                                                                    @if(isset($details['company_representatives_relation']) && $details['company_representatives_relation'] == 'family') selected @endif
                                                                >{{ __('Family') }}</option>
                                                                <option 
                                                                    value="friends"
                                                                    @if(isset($details['company_representatives_relation']) && $details['company_representatives_relation'] == 'friends') selected @endif
                                                                >{{ __('Friends') }}</option>
                                                                <option 
                                                                    value="partner"
                                                                    @if(isset($details['company_representatives_relation']) && $details['company_representatives_relation'] == 'partner') selected @endif
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
                                                                value="{{ isset($details['summary_relation']) ? $details['summary_relation'] : '' }}"
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
                                                                <option value="1" @if(isset($details['company_asset_range']) && $details['company_asset_range'] == 1) selected @endif
                                                                >< CHF 500.000</option>
                                                                <option value="2" @if(isset($details['company_asset_range']) && $details['company_asset_range'] == 2) selected @endif
                                                                >CHF 500.000 - 1.000.000</option>
                                                                <option value="3" @if(isset($details['company_asset_range']) && $details['company_asset_range'] == 3) selected @endif
                                                                >CHF 1.000.000 - 2.500.000</option>
                                                                <option value="4" @if(isset($details['company_asset_range']) && $details['company_asset_range'] == 4) selected @endif
                                                                >CHF 2.500.000 - 7.500.000</option>
                                                                <option value="5" @if(isset($details['company_asset_range']) && $details['company_asset_range'] == 5) selected @endif
                                                                >CHF 7.500.000 - 10.000.000</option>
                                                                <option value="6" @if(isset($details['company_asset_range']) && $details['company_asset_range'] == 6) selected @endif
                                                                >> CHF 10.000.000</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-6 col-md-12">
                                                        <label for="company_requested_service" class="form-label">{{ __('Requested services') }}</label>
                                                        <div class="input-group">
                                                            <select 
                                                                name="company_requested_service" 
                                                                id="company_requested_service" 
                                                                class="multiple-select"
                                                                multiple="multiple"
                                                            >
                                                                <option value="1">{{ __('Currency exchange') }}</option>
                                                                <option value="2">{{ __('Money transfers') }}</option>
                                                                <option value="3">{{ __('Trust Services Advisory') }}</option>
                                                                <option value="4">{{ __('Crypto exchange and cash out') }}</option>
                                                                <option value="5">{{ __('Payment and electronic money account') }}</option>
                                                                <option value="6">{{ __('Forex or Crypto Trading') }}</option>
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
                                                                value="{{ isset($details['transactions_expected']) ? $details['transactions_expected'] : '' }}"
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
                                                                value="{{ isset($details['transaction_amount']) ? $details['transaction_amount'] : '' }}"
                                                                placeholder=""                                                                
                                                            />
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-6 col-md-12">
                                                        <label for="purpose_relationship" class="form-label">{{ __('Purpose of the relationship') }}</label>
                                                        <div class="input-group">
                                                            <select 
                                                                name="purpose_relationship" 
                                                                id="purpose_relationship" 
                                                                class="multiple-select"
                                                                multiple="multiple"
                                                            >
                                                                <option value="1">{{ __('Crypto Exchange') }}</option>
                                                                <option value="2">{{ __('Trust Service') }}</option>
                                                                <option value="3">{{ __('Trading') }}</option>
                                                                <option value="4">{{ __('Training') }}</option>
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
                                                                value="{{ isset($details['bank_name']) ? $details['bank_name'] : '' }}"
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
                                                                    @if(isset($details['bank_country']) && $details['bank_country'] == $country['code']) selected @endif
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
                                                                value="{{ isset($details['bank_account']) ? $details['bank_account'] : '' }}"
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
                                                                value="{{ isset($details['bank_swift']) ? $details['bank_swift'] : '' }}"
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
                                                                    @if(isset($details['crypto_currency_used']) && $details['crypto_currency_used'] == 'btc') selected @endif
                                                                >BTC</option>
                                                                <option 
                                                                    value="eth"
                                                                    @if(isset($details['crypto_currency_used']) && $details['crypto_currency_used'] == 'eth') selected @endif
                                                                >ETH</option>
                                                                <option 
                                                                    value="usdte"
                                                                    @if(isset($details['crypto_currency_used']) && $details['crypto_currency_used'] == 'usdte') selected @endif
                                                                >USDT(ERC-20)</option>
                                                                <option 
                                                                    value="usdtt"
                                                                    @if(isset($details['crypto_currency_used']) && $details['crypto_currency_used'] == 'usdtt') selected @endif
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
                                                                value="{{ isset($details['wallet_address']) ? $details['wallet_address'] : '' }}"
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
                                                                @if(isset($details['company_income_1']) 
                                                                    && $details['company_income_1']) checked 
                                                                @endif
                                                            />
                                                            <label class="form-check-label" for="company_income_1">
                                                                {{ __('Income related to work (salary)') }}
                                                            </label>
                                                        </div>  
                                                        <div class="input-group">
                                                            <input
                                                                name="company_income_amount_1"
                                                                id="company_income_amount_1"
                                                                type="number"
                                                                class="form-control"                                                                
                                                                value="{{ isset($details['company_income_amount_1']) ? $details['company_income_amount_1'] : '' }}"
                                                                placeholder=""
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
                                                                @if(isset($details['company_income_2']) 
                                                                    && $details['company_income_2']) checked 
                                                                @endif
                                                            />
                                                            <label class="form-check-label" for="company_income_2">
                                                                {{ __('Income related to an independent activity') }}
                                                            </label>
                                                        </div>  
                                                        <div class="input-group">
                                                            <input
                                                                name="company_income_amount_2"
                                                                id="company_income_amount_2"
                                                                type="number"
                                                                class="form-control"                                                                
                                                                value="{{ isset($details['company_income_amount_2']) ? $details['company_income_amount_2'] : '' }}"
                                                                placeholder=""
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
                                                                @if(isset($details['company_income_3']) 
                                                                    && $details['company_income_3']) checked 
                                                                @endif
                                                            />
                                                            <label class="form-check-label" for="company_income_3">
                                                                {{ __('Income related to real estate') }}
                                                            </label>
                                                        </div>  
                                                        <div class="input-group">
                                                            <input
                                                                name="company_income_amount_3"
                                                                id="company_income_amount_3"
                                                                type="number"
                                                                class="form-control"                                                                
                                                                value="{{ isset($details['company_income_amount_3']) ? $details['company_income_amount_3'] : '' }}"
                                                                placeholder=""
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
                                                                @if(isset($details['company_income_4']) 
                                                                    && $details['company_income_4']) checked 
                                                                @endif
                                                            />
                                                            <label class="form-check-label" for="company_income_4">
                                                                {{ __('Annuity (pension, retirement, etc.)') }}
                                                            </label>
                                                        </div>  
                                                        <div class="input-group">
                                                            <input
                                                                name="company_income_amount_4"
                                                                id="company_income_amount_4"
                                                                type="number"
                                                                class="form-control"                                                                
                                                                value="{{ isset($details['company_income_amount_4']) ? $details['company_income_amount_4'] : '' }}"
                                                                placeholder=""
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
                                                                @if(isset($details['company_income_5']) 
                                                                    && $details['company_income_5']) checked 
                                                                @endif
                                                            />
                                                            <label class="form-check-label" for="company_income_5">
                                                                {{ __('Dividends / Interests on existing investments') }}
                                                            </label>
                                                        </div>  
                                                        <div class="input-group">
                                                            <input
                                                                name="company_income_amount_5"
                                                                id="company_income_amount_5"
                                                                type="number"
                                                                class="form-control"                                                                
                                                                value="{{ isset($details['company_income_amount_5']) ? $details['company_income_amount_5'] : '' }}"
                                                                placeholder=""
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
                                                                @if(isset($details['company_income_6']) 
                                                                    && $details['company_income_6']) checked 
                                                                @endif
                                                            />
                                                            <label class="form-check-label" for="company_income_6">
                                                                {{ __('Inheritance') }}
                                                            </label>
                                                        </div>  
                                                        <div class="input-group">
                                                            <input
                                                                name="company_income_amount_6"
                                                                id="company_income_amount_6"
                                                                type="number"
                                                                class="form-control"                                                                
                                                                value="{{ isset($details['company_income_amount_6']) ? $details['company_income_amount_6'] : '' }}"
                                                                placeholder=""
                                                            />
                                                        </div>                                                      
                                                    </div>
                                                    <div class="col-xl-6 col-md-12">
                                                        <div>
                                                            <input
                                                                class="form-check-input check-input"
                                                                type="checkbox"
                                                                name="company_income_7"
                                                                id="company_income_7"
                                                                @if(isset($details['company_income_7']) 
                                                                    && $details['company_income_7']) checked 
                                                                @endif
                                                            />
                                                            <label class="form-check-label" for="company_income_7">
                                                                {{ __('Other (please specify)') }}
                                                            </label>
                                                        </div>  
                                                        <div class="input-group">
                                                            <input
                                                                name="company_income_other"
                                                                id="company_income_other"
                                                                type="text"
                                                                class="form-control"                                                                
                                                                value="{{ isset($details['company_income_other']) ? $details['company_income_other'] : '' }}"
                                                                placeholder="{{ __('Please specify)') }}"
                                                            />
                                                        </div>   
                                                        <div class="input-group">
                                                            <input
                                                                name="company_income_amount_7"
                                                                id="company_income_amount_7"
                                                                type="number"
                                                                class="form-control"                                                                
                                                                value="{{ isset($details['company_income_amount_7']) ? $details['company_income_amount_7'] : '' }}"
                                                                placeholder=""
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
                                                                value="{{ isset($details['origin_wealth']) ? $details['origin_wealth'] : '' }}"
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
                                                                value="{{ isset($details['source_cscm']) ? $details['source_cscm'] : '' }}"
                                                                placeholder=""                                                                
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
        $("form").submit(function(e) {
            e.preventDefault();
            var v= $(this).serialize();
            console.log(v);                                        
        });
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
                // $('#kyc_form').validate({ 
                //     ignore: [],
                //     rules: {
                //         company_requested_service: {
                //             required: true
                //         },
                //         transactions_expected: {
                //             required: true
                //         },
                //         transaction_amount: {
                //             required: true
                //         },
                //         purpose_relationship: {
                //             required: true
                //         },
                //         bank_name: {
                //             required: true
                //         },
                //         bank_account: {
                //             required: true
                //         },
                //         bank_swift: {
                //             required: true
                //         },
                //         wallet_address: {
                //             required: true
                //         },
                //     },
                //     errorPlacement: function(error, element) {
                //         error.insertAfter(element);           
                //     },
                //     submitHandler: function(form) {
                //         // form.submit();

                //         var data = {};
                //         var dataArray = form.serializeArray();
                //         for(var i=0;i<dataArray.length;i++){
                //             data[dataArray[i].name] = dataArray[i].value;
                //         }

                //         console.log(data);
                //         return false;
                //     }
                // });
            });
        </script> 
        @endif
    @endif
@endsection
