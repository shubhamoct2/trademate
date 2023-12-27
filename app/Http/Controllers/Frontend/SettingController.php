<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Traits\ImageUpload;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PragmaRX\Google2FALaravel\Support\Authenticator;
use Auth;

class SettingController extends Controller
{
    use ImageUpload;

    public function settings()
    {
        return view('frontend::user.setting.index');
    }

    public function withdrawalUpdate(Request $request) {
        $validator = Validator::make($request->all(), [
            'currency' => 'required|string',
            'blockchain' => 'string',
            'address' => 'required|string'
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first(), 'Error');

            return redirect()->back();
        }

        $input = $request->all();

        $withdrawal_address = [
            'currency' => $input['currency'],
            'address' => $input['address']
        ];

        if ($input['currency'] == 'usdt') {
            $withdrawal_address['blockchain'] = $input['blockchain'];
        }

        /* validation for wallet address */
        $validFlag = false;
        if ($withdrawal_address['currency'] == 'btc') {
            // $validFlag = preg_match('/^[13][a-km-zA-HJ-NP-Z1-9]{25,34}$/', $withdrawal_address['address']); //mainet
            $validFlag = preg_match('/\b(tb(0([ac-hj-np-z02-9]{39}|[ac-hj-np-z02-9]{59})|1[ac-hj-np-z02-9]{8,87})|[mn2][a-km-zA-HJ-NP-Z1-9]{25,39})\b/', $withdrawal_address['address']);
        } else if ($withdrawal_address['currency'] == 'eth') {
            $validFlag = preg_match('/^0x[a-fA-F0-9]{40}$/', $withdrawal_address['address']);
        } else if ($withdrawal_address['currency'] == 'usdt') {
            if ($withdrawal_address['blockchain'] == 'erc20') {
                $validFlag = preg_match('/^0x[a-fA-F0-9]{40}$/', $withdrawal_address['address']);
            } else if ($withdrawal_address['blockchain'] == 'trc20') {
                $validFlag = preg_match('/^T[a-zA-Z0-9]{33}$/', $withdrawal_address['address']);
            }
        }   

        if ($validFlag == false) {
            notify()->error(__('Invalid wallet address'), 'Error');

            return redirect()->back();
        }

        $user = Auth::user();
        $user->withdrawal_address = json_encode($withdrawal_address);
        $user->update();

        notify()->success('Your Withdrawal Address Is Updated successfully');

        return redirect()->route('user.setting.show');
    }

    public function profileUpdate(Request $request)
    {
        $input = $request->all();
        $user = \Auth::user();

        if ($user->editable_profile) {
            $validator = Validator::make($request->all(), [
                'first_name' => 'required',
                'last_name' => 'required',
                'username' => 'unique:users,username,'.$user->id,
                'gender' => 'required',
                'city' => 'required',
                'zip_code' => 'required',
                'address' => 'required',
                'date_of_birth' => 'date',
                'phone' => 'required',
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'phone' => 'required',
            ]);
        }

        if ($validator->fails()) {
            notify()->error($validator->errors()->first(), 'Error');
            return redirect()->back();
        }

        if ($user->editable_profile) {
            $data = [
                'avatar' => $request->hasFile('avatar') ? self::imageUploadTrait($input['avatar'], $user->avatar) : $user->avatar,
                'first_name' => $input['first_name'],
                'last_name' => $input['last_name'],
                'username' => $input['username'],
                'gender' => $input['gender'],
                'date_of_birth' => $input['date_of_birth'] == '' ? null : $input['date_of_birth'],
                'phone' => $input['phone'],
                'city' => $input['city'],
                'zip_code' => $input['zip_code'],
                'address' => $input['address'],
                'editable_profile' => 0,
            ];
        } else {
            $data = [
                'phone' => $input['phone'],
            ];
        }

        $user->update($data);

        notify()->success('Your Profile Updated successfully');
        return redirect()->route('user.setting.show');
    }

    public function twoFa()
    {
        $user = \Auth::user();
        $google2fa = app('pragmarx.google2fa');
        $secret = $google2fa->generateSecretKey();

        $user->update([
            'google2fa_secret' => $secret,
        ]);
        notify()->success(__('QR Code And Secret Key Generate successfully'));

        return redirect()->back();

    }

    public function actionTwoFa(Request $request)
    {
        $user = \Auth::user();

        if ($request->status == 'disable') {

            if (Hash::check(request('one_time_password'), $user->password)) {
                $user->update([
                    'two_fa' => 0,
                ]);
                notify()->success(__('2Fa Authentication Disable successfully'));

                return redirect()->back();
            }

            notify()->warning(__('Wrong Your Password'));

            return redirect()->back();

        } elseif ($request->status == 'enable') {
            session([
                config('google2fa.session_var') => [
                    'auth_passed' => false,
                ],
            ]);

            $authenticator = app(Authenticator::class)->boot($request);
            if ($authenticator->isAuthenticated()) {

                $user->update([
                    'two_fa' => 1,
                ]);
                notify()->success(__('2Fa Authentication Enable successfully'));

                return redirect()->back();

            }

            notify()->warning(__('2Fa Authentication Wrong One Time Key'));

            return redirect()->back();
        }
    }
}
