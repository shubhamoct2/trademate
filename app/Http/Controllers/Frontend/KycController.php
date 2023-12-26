<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;

use App\Enums\KycStatus;

use App\Models\KycInfo;

use App\Traits\ImageUpload;
use App\Traits\NotifyTrait;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use Validator;
use Auth;
use Carbon\Carbon;

class KycController extends Controller
{
    use ImageUpload, NotifyTrait;

    public function kyc()
    {
        $user = Auth::user();
        $kycInfo = $user->kycInfo;

        $max_step = 5;

        if ($kycInfo) {
            $kycStatus = $kycInfo->status->name;
            $step = $kycInfo->data['step'];
            $details = $kycInfo->data;
        } else {
            $kycStatus = null;
            $step = 0;
            $details = [];
        }

        return view('frontend::user.kyc.index', compact(
            'step',
            'max_step',
            'kycStatus',
            'details',
        ));
    }

    public function kycData($id)
    {
        $fields = Kyc::find($id)->fields;

        return view('frontend::user.kyc.data', compact('fields'))->render();
    }

    public function back(Request $request) {
        $user = Auth::user();

        if ($user->kycInfo) {
            $kycInfo = $user->kycInfo;

            $data = $kycInfo->data;
            $data['step'] = $data['step'] - 1;

            $kycInfo->update([
                'data' => $data
            ]);
        }

        return redirect()->back();
    }

    public function submit(Request $request) {
        $user = Auth::user();
        $input = $request->all();

        // Basic Validation
        $validator = Validator::make($input, [
            'step' => 'required|integer',
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first(), 'Error');
            return redirect()->back();
        }

        // Get base information
        $step = intval($input['step']);
        
        if ($step == 0) {
            $validator = Validator::make($input, [
                'kyc_type' => 'required|string',
            ]);

            if ($validator->fails()) {
                notify()->error($validator->errors()->first(), 'Error');
                return redirect()->back();
            }

            $kycInfo = $user->kycInfo;
            if (is_null($kycInfo)) {
                $kycInfo = KycInfo::create([
                    'status' => KycStatus::Draft,
                    'data' => [
                        'step' => $step + 1,
                        'kyc_type' => $input['kyc_type'],
                    ]
                ]);

                $user->update([
                    'kyc_info_id' => $kycInfo->id,
                ]);
            } else {
                $data = $kycInfo->data;
                $data['step'] = $step + 1;
                $data['kyc_type'] = $input['kyc_type'];

                $kycInfo->update([
                    'data' => $data,
                ]);
            }
        } else {
            // Get KYC information from database
            $kycInfo = $user->kycInfo;
            $data = $kycInfo->data;

            if ($step == 1) {
                if ($kycInfo->data['kyc_type'] == "individual") {
                    $validator = Validator::make($input, [
                        'relationship_name' => 'required|string|min:3',
                        'recommended_by' => 'string|min:3',
                        'summary_relation' => 'required|string|min:3',
                    ]);

                    if ($validator->fails()) {
                        notify()->error($validator->errors()->first(), 'Error');
                        return redirect()->back();
                    }

                    $data['general'] = [];
                    $data['general']['relationship_name'] = $input['relationship_name'];
                    $data['general']['summary_relation'] = $input['summary_relation'];
                    if ($input['recommended_by']) {
                        $data['general']['recommended_by'] = $input['recommended_by'];
                    }    
                } else {
                    $validator = Validator::make($input, [
                        'company_name' => 'required|string|min:3',
                        'company_client_1' => 'required|string|min:3',
                        'company_representative_1' => 'required|string|min:3',
                        'summary_relation' => 'required|string|min:3',
                    ]);

                    if ($validator->fails()) {
                        notify()->error($validator->errors()->first(), 'Error');
                        return redirect()->back();
                    }

                    if ($input['company_client_2'] != '' || $input['company_client_3'] != '') {
                        if ($input['company_clients_relation'] == '') {
                            notify()->error(__('Link between the clients is required.'), 'Error');
                            return redirect()->back();
                        }
                    }

                    if ($input['company_representative_2'] != '') {
                        if ($input['company_representatives_relation'] == '') {
                            notify()->error(__('Link between the representatives is required.'), 'Error');
                            return redirect()->back();
                        }
                    }

                    $data['general'] = [];
                    $data['general']['company_name'] = $input['company_name'];
                    $data['general']['company_client_1'] = $input['company_client_1'];
                    $data['general']['company_representative_1'] = $input['company_representative_1'];
                    $data['general']['summary_relation'] = $input['summary_relation'];

                    if ($input['company_client_2'] != '') $data['general']['company_client_2'] = $input['company_client_2'];
                    if ($input['company_client_3'] != '') $data['general']['company_client_3'] = $input['company_client_3'];
                    if ($input['company_representative_2'] != '') $data['general']['company_representative_2'] = $input['company_representative_2'];
                    if ($input['company_clients_relation'] != '') $data['general']['company_clients_relation'] = $input['company_clients_relation'];
                    if ($input['company_representatives_relation'] != '') $data['general']['company_representatives_relation'] = $input['company_representatives_relation'];
                }
            } else if ($step == 2) {
                $validator = Validator::make($input, [
                    'company_asset_range' => 'required',
                    'company_requested_service' => 'required|array|min:1',
                    'transactions_expected' => 'required|numeric',
                    'transaction_amount' => 'required|numeric',
                    'purpose_relationship' => 'required|array|min:1',
                    'bank_name' => 'required|string|min:3',
                    'bank_country' => 'required',
                    'bank_account' => 'required|string|min:3',
                    'bank_swift' => 'required|string|min:3',
                    'crypto_currency' => 'required',
                    'wallet_address' => 'required|string',
                ]);

                if ($validator->fails()) {
                    notify()->error($validator->errors()->first(), 'Error');
                    return redirect()->back();
                }

                /* validation for wallet address */
                $validFlag = false;
                if ($input['crypto_currency'] == 'btc') {
                    $validFlag = preg_match('/^[13][a-km-zA-HJ-NP-Z1-9]{25,34}$/', $input['wallet_address']); //mainet
                    // $validFlag = preg_match('/\b(tb(0([ac-hj-np-z02-9]{39}|[ac-hj-np-z02-9]{59})|1[ac-hj-np-z02-9]{8,87})|[mn2][a-km-zA-HJ-NP-Z1-9]{25,39})\b/', $input['wallet_address']);
                } else if ($input['crypto_currency'] == 'eth') {
                    $validFlag = preg_match('/^0x[a-fA-F0-9]{40}$/', $input['wallet_address']);
                } else if ($input['crypto_currency'] == 'usdte') {
                    $validFlag = preg_match('/^0x[a-fA-F0-9]{40}$/', $input['wallet_address']);
                }  else if ($input['crypto_currency'] == 'usdtt') {
                    $validFlag = preg_match('/^T[a-zA-Z0-9]{33}$/', $input['wallet_address']);
                }

                if ($validFlag == false) {
                    notify()->error(__('Invalid wallet address'), 'Error');            
                    return redirect()->back();
                }

                $data['asset'] = [];
                $data['asset']['company_asset_range'] = $input['company_asset_range'];
                $data['asset']['company_requested_service'] = $input['company_requested_service'];
                $data['asset']['transactions_expected'] = $input['transactions_expected'];
                $data['asset']['transaction_amount'] = $input['transaction_amount'];
                $data['asset']['purpose_relationship'] = $input['purpose_relationship'];
                $data['asset']['bank_name'] = $input['bank_name'];
                $data['asset']['bank_country'] = $input['bank_country'];
                $data['asset']['bank_account'] = $input['bank_account'];
                $data['asset']['bank_swift'] = $input['bank_swift'];
                $data['asset']['crypto_currency'] = $input['crypto_currency'];
                $data['asset']['wallet_address'] = $input['wallet_address'];
            } else if ($step == 3) {
                $validator = Validator::make($input, [
                    'company_income_1' => 'nullable',
                    'company_income_1_amount' => 'numeric',
                    'company_income_2' => 'nullable',
                    'company_income_2_amount' => 'numeric',
                    'company_income_3' => 'nullable',
                    'company_income_3_amount' => 'numeric',
                    'company_income_4' => 'nullable',
                    'company_income_4_amount' => 'numeric',
                    'company_income_5' => 'nullable',
                    'company_income_5_amount' => 'numeric',
                    'company_income_6' => 'nullable',
                    'company_income_6_amount' => 'numeric',
                    'company_income_other' => 'nullable',
                    'company_income_other_description' => 'string',
                    'company_income_other_amount' => 'numeric',
                    'origin_wealth' => 'required',
                    'source_cscm' => 'required',
                ]);

                if ($validator->fails()) {
                    notify()->error($validator->errors()->first(), 'Error');
                    return redirect()->back();
                }

                if (isset($input['company_income_1']) && $input['company_income_1'] == "on") {
                    $input['company_income_1'] = 1;
                } else {
                    $input['company_income_1'] = 0;
                }

                if (isset($input['company_income_2']) && $input['company_income_2'] == "on") {
                    $input['company_income_2'] = 1;
                } else {
                    $input['company_income_2'] = 0;
                }

                if (isset($input['company_income_3']) && $input['company_income_3'] == "on") {
                    $input['company_income_3'] = 1;
                } else {
                    $input['company_income_3'] = 0;
                }

                if (isset($input['company_income_4']) && $input['company_income_4'] == "on") {
                    $input['company_income_4'] = 1;
                } else {
                    $input['company_income_4'] = 0;
                }

                if (isset($input['company_income_5']) && $input['company_income_5'] == "on") {
                    $input['company_income_5'] = 1;
                } else {
                    $input['company_income_5'] = 0;
                }

                if (isset($input['company_income_6']) && $input['company_income_6'] == "on") {
                    $input['company_income_6'] = 1;
                } else {
                    $input['company_income_6'] = 0;
                }

                if (isset($input['company_income_other']) && $input['company_income_other'] == "on") {
                    $input['company_income_other'] = 1;
                } else {
                    $input['company_income_other'] = 0;
                }

                $data['income'] = [];
                $data['income']['company_income_1'] = [
                    'checked' => $input['company_income_1'],
                    'amount' => $input['company_income_1'] ?  floatval($input['company_income_1_amount']) : null,
                ];
                $data['income']['company_income_2'] = [
                    'checked' => $input['company_income_2'],
                    'amount' => $input['company_income_2'] ?  floatval($input['company_income_2_amount']) : null,
                ];
                $data['income']['company_income_3'] = [
                    'checked' => $input['company_income_3'],
                    'amount' => $input['company_income_3'] ?  floatval($input['company_income_3_amount']) : null,
                ];
                $data['income']['company_income_4'] = [
                    'checked' => $input['company_income_4'],
                    'amount' => $input['company_income_4'] ?  floatval($input['company_income_4_amount']) : null,
                ];
                $data['income']['company_income_5'] = [
                    'checked' => $input['company_income_5'],
                    'amount' => $input['company_income_5'] ?  floatval($input['company_income_5_amount']) : null,
                ];
                $data['income']['company_income_6'] = [
                    'checked' => $input['company_income_6'],
                    'amount' => $input['company_income_6'] ?  floatval($input['company_income_6_amount']) : null,
                ];
                $data['income']['company_income_other'] = [
                    'checked' => $input['company_income_other'],
                    'amount' => $input['company_income_other'] ?  floatval($input['company_income_other_amount']) : null,
                    'description' => $input['company_income_other'] ?  $input['company_income_other_description'] : null,
                ];

                $data['income']['origin_wealth'] = $input['origin_wealth'];
                $data['income']['source_cscm'] = $input['source_cscm'];
            } else if ($step == 4) {
                $validator = Validator::make($input, [
                    'kyc_credential_file' => 'required',
                    'first_name' => 'required',
                    'last_name' => 'required',
                    'birth_date' => 'required',
                    'gender' => 'required',
                    'residential_address' => 'required',
                    'passport_number' => 'required',
                    'tax_other' => 'nullable'
                ]);

                if ($validator->fails()) {
                    notify()->error($validator->errors()->first(), 'Error');
                    return redirect()->back();
                }

                if (isset($input['tax_other']) && $input['tax_other'] == "on") {
                    $input['tax_other'] = 1;
                } else {
                    $input['tax_other'] = 0;
                }

                if ($input['tax_other'] == 0) {
                    $validator = Validator::make($input, [
                        'tax_country_1' => 'required',
                        'tax_id_1' => 'required',
                    ]);
                }

                if ($validator->fails()) {
                    notify()->error($validator->errors()->first(), 'Error');
                    return redirect()->back();
                }

                $data['personal'] = [];
                $data['personal']['file'] = self::imageUploadTrait($input['kyc_credential_file']);

                $data['personal']['first_name'] = $input['first_name'];
                $data['personal']['last_name'] = $input['last_name'];
                $data['personal']['birth_date'] = $input['birth_date'];
                $data['personal']['gender'] = $input['gender'];
                $data['personal']['residential_address'] = $input['residential_address'];
                $data['personal']['passport_number'] = $input['passport_number'];
                $data['personal']['tax_country_1'] = $input['tax_country_1'];
                $data['personal']['tax_id_1'] = $input['tax_id_1'];
                $data['personal']['tax_country_2'] = $input['tax_country_2'];
                $data['personal']['tax_id_2'] = $input['tax_id_2'];
                $data['personal']['tax_country_3'] = $input['tax_country_3'];
                $data['personal']['tax_id_3'] = $input['tax_id_3'];
                $data['personal']['tax_other'] = [
                    'checked' => $input['tax_other'],
                    'description' => $input['tax_other_reason'] ?  $input['tax_other_reason'] : null,
                ];
            } else if ($step == 5) {
                $data['kyc_date'] = Carbon::now()->format('Y-m-d');

                $kycInfo->update([
                    'status' => KycStatus::Pending
                ]);

                $shortcodes = [
                    '[[full_name]]' => $user->full_name,
                    '[[email]]' => $user->email,
                    '[[site_title]]' => setting('site_title', 'global'),
                    '[[site_url]]' => route('home'),
                    '[[kyc_type]]' => ucwords($data['kyc_type']),
                    '[[status]]' => 'Pending',
                ];
        
                $this->mailNotify(setting('site_email', 'global'), 'kyc_request', $shortcodes);
                $this->pushNotify('kyc_request', $shortcodes, route('admin.kyc.pending'), $user->id);
        
                notify()->success(__(' KYC Requested Successfully!'));
            }

            if ($step != 5)
                $data['step'] += 1;

            $kycInfo->update([
                'data' => $data
            ]);
        }

        return redirect()->back();
    }
}
