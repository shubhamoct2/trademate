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

class KycController extends Controller
{
    use ImageUpload, NotifyTrait;

    public function kyc()
    {
        $user = Auth::user();

        $kycStatus = $user->kycInfo ? $user->kycInfo->status->name : null;
        $max_step = 5;

        $step = $user->kycInfo ? $user->kycInfo->data['step'] : 0;
        $details = $user->kycInfo ? $user->kycInfo->data : [];

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

        Log::info(json_encode($input));

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
            // Get KYC information from database
            $kycInfo = $user->kycInfo;
            if ($kycInfo->data['kyc_type'] == "company") {
                $data = $kycInfo->data;

                if ($step == 1) {
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

                    $data['company_name'] = $input['company_name'];
                    $data['company_client_1'] = $input['company_client_1'];
                    $data['company_representative_1'] = $input['company_representative_1'];
                    $data['summary_relation'] = $input['summary_relation'];

                    if ($input['company_client_2'] != '') $data['company_client_2'] = $input['company_client_2'];
                    if ($input['company_client_3'] != '') $data['company_client_3'] = $input['company_client_3'];
                    if ($input['company_representative_2'] != '') $data['company_representative_2'] = $input['company_representative_2'];
                    if ($input['company_clients_relation'] != '') $data['company_clients_relation'] = $input['company_clients_relation'];
                    if ($input['company_representatives_relation'] != '') $data['company_representatives_relation'] = $input['company_representatives_relation'];
                } else if ($step == 2) {
                    $validator = Validator::make($input, [
                        'company_asset_range' => 'required',
                        'company_requested_service' => 'required',
                        'transactions_expected' => 'required|numeric',
                        'transaction_amount' => 'required|numeric',
                        'purpose_relationship' => 'required',
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

                    $data['company_asset_range'] = $input['company_asset_range'];
                    $data['company_requested_service'] = $input['company_requested_service'];
                    $data['transactions_expected'] = $input['transactions_expected'];
                    $data['transaction_amount'] = $input['transaction_amount'];
                    $data['purpose_relationship'] = $input['purpose_relationship'];
                    $data['bank_name'] = $input['bank_name'];
                    $data['bank_country'] = $input['bank_country'];
                    $data['bank_account'] = $input['bank_account'];
                    $data['bank_swift'] = $input['bank_swift'];
                    $data['crypto_currency'] = $input['crypto_currency'];
                    $data['wallet_address'] = $input['wallet_address'];
                }

                $data['step'] += 1;

                $kycInfo->update([
                    'data' => $data
                ]);
            }
        }

        return redirect()->back();
    }

    /*
    public function submit(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'kyc_id' => 'required',
            'kyc_credential' => 'required',
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first(), 'Error');

            return redirect()->back();
        }

        $kyc = Kyc::find($input['kyc_id']);

        $kycCredential = array_merge($input['kyc_credential'], ['kyc_type_of_name' => $kyc->name, 'kyc_time_of_time' => now()]);

        $user = \Auth::user();

        if ($user->kyc_credential) {
            foreach (json_decode($user->kyc_credential, true) as $key => $value) {
                self::delete($value);
            }
        }
        foreach ($kycCredential as $key => $value) {

            if (is_file($value)) {
                $kycCredential[$key] = self::imageUploadTrait($value);
            }
        }

        $user->update([
            'kyc_credential' => json_encode($kycCredential),
            'kyc' => KYCStatus::Pending,
        ]);
        $shortcodes = [
            '[[full_name]]' => $user->full_name,
            '[[email]]' => $user->email,
            '[[site_title]]' => setting('site_title', 'global'),
            '[[site_url]]' => route('home'),
            '[[kyc_type]]' => $kyc->name,
            '[[status]]' => 'Pending',
        ];

        $this->mailNotify(setting('site_email', 'global'), 'kyc_request', $shortcodes);
        $this->pushNotify('kyc_request', $shortcodes, route('admin.kyc.pending'), $user->id);

        notify()->success(__(' KYC Updated'));

        return redirect()->route('user.kyc');
    }
    */
}
