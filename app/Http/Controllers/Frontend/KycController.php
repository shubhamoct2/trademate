<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;

use App\Enums\KycStatus;

use App\Models\KycInfo;

use App\Traits\ImageUpload;
use App\Traits\NotifyTrait;

use Illuminate\Http\Request;
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

        return view('frontend::user.kyc.index', compact(
            'step',
            'max_step',
            'kycStatus',
        ));
    }

    public function kycData($id)
    {
        $fields = Kyc::find($id)->fields;

        return view('frontend::user.kyc.data', compact('fields'))->render();
    }

    public function submit(Request $request) {
        $user = Auth::user();
        $input = $request->all();

        $validator = Validator::make($input, [
            'step' => 'required|integer',
            'direction' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first(), 'Error');
            return redirect()->back();
        }

        $step = intval($input['step']);
        $direction = intval($input['direction']);

        if ($direction) {
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
