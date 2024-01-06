<?php

namespace App\Http\Controllers\Backend;

use App\Enums\KycStatus;
use App\Http\Controllers\Controller;
use App\Models\Kyc;
use App\Models\KycInfo;
use App\Models\User;
use App\Traits\NotifyTrait;
use DataTables;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class KycController extends Controller
{
    use NotifyTrait;

    /**
     * Display a listing of the resource.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('permission:kyc-form-manage', ['only' => ['create', 'store', 'show', 'edit', 'update', 'destroy']]);
        $this->middleware('permission:kyc-list', ['only' => ['KycPending', 'kycAll', 'KycRejected']]);
        $this->middleware('permission:kyc-action', ['only' => ['depositAction', 'actionNow']]);

    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        $kycs = KycInfo::all();

        return view('backend.kyc.index', compact('kycs'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return string
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required|unique:kycs,name',
            'status' => 'required',
            'fields' => 'required',
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first(), 'Error');

            return redirect()->back();
        }

        $data = [
            'name' => $input['name'],
            'status' => $input['status'],
            'fields' => json_encode($input['fields']),
        ];

        $kyc = Kyc::create($data);
        notify()->success($kyc->name.' '.__(' KYC Created'));

        return redirect()->route('admin.kyc-form.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        return view('backend.kyc.create');
    }

    /**
     * Display the specified resource.
     *
     * @return Application|Factory|View
     */
    public function show(Kyc $kyc)
    {
        return view('backend.kyc.edit', compact('kyc'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Application|Factory|View
     */
    public function edit($id)
    {
        $kyc = Kyc::find($id);

        return view('backend.kyc.edit', compact('kyc'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return RedirectResponse
     */
    public function destroy($id)
    {
        Kyc::find($id)->delete();
        notify()->success(__('KYC Deleted Successfully'));

        return redirect()->route('admin.kyc-form.index');
    }

    /**
     * @return Application|Factory|View|JsonResponse
     *
     * @throws Exception
     */
    public function KycPending(Request $request)
    {

        if ($request->ajax()) {
            $data = KycInfo::where('status', KycStatus::Pending->value)->with('user')->orderBy('updated_at', 'desc')->get();

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('time', 'backend.kyc.include.__time')
                ->addColumn('user', 'backend.kyc.include.__user')
                ->addColumn('type', 'backend.kyc.include.__type')
                ->addColumn('status', 'backend.kyc.include.__status')
                ->addColumn('action', 'backend.kyc.include.__action')
                ->rawColumns(['time', 'user', 'type', 'status', 'action'])
                ->make(true);
        }

        return view('backend.kyc.pending');
    }

    /**
     * @return Application|Factory|View|JsonResponse
     *
     * @throws Exception
     */
    public function KycRejected(Request $request)
    {

        if ($request->ajax()) {
            $data = KycInfo::where('status', KycStatus::Failed->value)->with('user')->orderBy('updated_at', 'desc')->get();

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('time', 'backend.kyc.include.__time')
                ->addColumn('user', 'backend.kyc.include.__user')
                ->addColumn('type', 'backend.kyc.include.__type')
                ->addColumn('status', 'backend.kyc.include.__status')
                ->addColumn('action', 'backend.kyc.include.__action')
                ->rawColumns(['time', 'user', 'type', 'status', 'action'])
                ->make(true);
        }

        return view('backend.kyc.rejected');
    }

    public function KycVerified(Request $request)
    {

        if ($request->ajax()) {
            $data = KycInfo::where('status', KycStatus::Verified->value)->with('user')->orderBy('updated_at', 'desc')->get();

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('time', 'backend.kyc.include.__time')
                ->addColumn('user', 'backend.kyc.include.__user')
                ->addColumn('type', 'backend.kyc.include.__type')
                ->addColumn('status', 'backend.kyc.include.__status')
                ->addColumn('action', 'backend.kyc.include.__action')
                ->rawColumns(['time', 'user', 'type', 'status', 'action'])
                ->make(true);
        }

        return view('backend.kyc.verified');
    }

    /**
     * @return string
     */
    public function showDetails($id)
    {
        $kycInfo = KycInfo::find($id);
        $user = $kycInfo->user;

        return view('backend.kyc.include.__kyc_data', compact('kycInfo', 'id', 'user'))->render();
    }

    /**
     * @return RedirectResponse
     */
    public function actionNow(Request $request)
    {
        $input = $request->all();

        $kycInfo = KycInfo::find($input['id']);
        $user = $kycInfo->user;
        
        $data = $kycInfo->data;
        $data['action_message'] = $input['message'];

        $kycInfo->update([
            'status' => $input['status'],
            'data' => $data,
        ]);

        $shortcodes = [
            '[[full_name]]' => $user->full_name,
            '[[email]]' => $user->email,
            '[[site_title]]' => setting('site_title', 'global'),
            '[[site_url]]' => route('home'),
            '[[kyc_type]]' => ucfirst($kycInfo->data['kyc_type']),
            '[[message]]' => $input['message'],
            '[[status]]' => $input['status'],
        ];
        
        if (intval($input['status']) == 1) {
            $this->mailNotify($user->email, 'kyc_action', $shortcodes);
            $this->smsNotify('kyc_action', $shortcodes, $user->phone);
            $this->pushNotify('kyc_action', $shortcodes, route('user.kyc'), $user->id);
        } else {
            $this->mailNotify($user->email, 'kyc_action_reject', $shortcodes);
            $this->smsNotify('kyc_action_reject', $shortcodes, $user->phone);
            $this->pushNotify('kyc_action_reject', $shortcodes, route('user.kyc'), $user->id);
        }

        notify()->success(__('KYC Update Successfully'));

        return redirect()->route('admin.kyc.all');
    }

    /**
     * Update the specified resource in storage.
     *
     * @return RedirectResponse
     */
    public function update(Request $request, $id)
    {

        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required|unique:kycs,name,'.$id,
            'status' => 'required',
            'fields' => 'required',
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first(), 'Error');

            return redirect()->back();
        }

        $data = [
            'name' => $input['name'],
            'status' => $input['status'],
            'fields' => json_encode($input['fields']),
        ];

        $kyc = Kyc::find($id);
        $kyc->update($data);
        notify()->success($kyc->name.' '.__(' KYC Updated'));

        return redirect()->route('admin.kyc-form.index');
    }

    /**
     * @return Application|Factory|View|JsonResponse
     *
     * @throws Exception
     */
    public function kycAll(Request $request)
    {

        if ($request->ajax()) {
            // $data = KycInfo::where('status', '<>', KycStatus::Draft)->with('user')->orderBy('updated_at', 'desc')->get();
            $data = KycInfo::whereNotNull('status')->with('user')->orderBy('updated_at', 'desc')->get();

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('time', 'backend.kyc.include.__time')
                ->addColumn('user', 'backend.kyc.include.__user')
                ->addColumn('type', 'backend.kyc.include.__type')
                ->addColumn('status', 'backend.kyc.include.__status')
                ->addColumn('action', 'backend.kyc.include.__action')
                ->rawColumns(['time', 'user', 'type', 'status', 'action'])
                ->make(true);
        }

        return view('backend.kyc.all');
    }

    public function downloadKycDetails($id) {
        $kycInfo = KycInfo::find($id);
        $user = $kycInfo->user;

        if (is_null($kycInfo) || is_null($user)) {
            return null;
        }

        $full_name = $kycInfo->data['personal']['first_name'] . ' ' . $kycInfo->data['personal']['last_name'];

        $pdf = Pdf::loadView('backend.kyc.include.__kyc_download_detail', compact('kycInfo', 'user'));
        return $pdf->download($full_name . '.pdf');
    }

    public function markAsDraft($id) {
        $kycInfo = KycInfo::find($id);
        $user = $kycInfo->user;

        if (is_null($kycInfo) || is_null($user)) {
            return null;
        }

        $kycInfo->update([
            'status' => KycStatus::Draft
        ]);

        return redirect()->back();
    }

    public function fileDownload($id) {
        $kycInfo = KycInfo::find($id);

        if (is_null($kycInfo)) {
            return null;
        }

        if (isset($kycInfo->data['personal']['file'])) {
            if (file_exists('assets/'.$kycInfo->data['personal']['file'])) {
                $full_name = $kycInfo->data['personal']['first_name'] . ' ' . $kycInfo->data['personal']['last_name'];
                $file = 'assets/' . $kycInfo->data['personal']['file'];
                return response()->download($file, $full_name.'.pdf');
            }
        }

        return null;
    }
}
