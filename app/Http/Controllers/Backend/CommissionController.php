<?php

namespace App\Http\Controllers\Backend;

use App\Enums\TxnStatus;
use App\Enums\TxnType;
use App\Http\Controllers\Controller;
use App\Models\LevelReferral;
use App\Models\Transaction;
use App\Models\User;
use App\Traits\NotifyTrait;
use DataTables;
use Exception;
use Hash;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Txn;
use Illuminate\Support\Facades\Log;

class CommissionController extends Controller
{
    use NotifyTrait;

    /**
     * Display a listing of the resource.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('permission:customer-commission-manage');
    }

    /**
     * @return Application|Factory|View|JsonResponse
     *
     * @throws Exception
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Transaction::where('type', TxnType::SendCommission)->latest();

            return Datatables::of($data)
                ->addIndexColumn()
                ->editColumn('status', 'backend.commission.include.__txn_status')
                ->editColumn('final_amount', 'backend.commission.include.__txn_amount')
                ->editColumn('charge', function ($request) {
                    return $request->charge.' '.setting('site_currency', 'global');
                })
                ->addColumn('username', 'backend.commission.include.__user')
                ->addColumn('action', 'backend.commission.include.__action')
                ->rawColumns(['status', 'description', 'final_amount', 'username', 'action'])
                ->make(true);
        }

        return view('backend.commission.index');
    }
}
