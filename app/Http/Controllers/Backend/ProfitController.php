<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;

use App\Models\Transaction;
use App\Models\User;
use App\Models\AutoTask;

use App\Enums\TxnType;
use App\Enums\AutoTaskType;
use App\Enums\TxnStatus;

use App\Traits\NotifyTrait;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

use App\DataTables\ProfitsDataTable;
use App\DataTables\ProfitDistributeDataTable;

use DataTables;
use Exception;
use Txn;

use App\Jobs\SendProfitShareJob;

use Carbon\Carbon;

class ProfitController extends Controller
{
    use NotifyTrait;

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function __construct()
    {
        $this->middleware('permission:profit-list');

    }

    /**
     * @return Application|Factory|View|JsonResponse
     *
     * @throws Exception
     */
    public function allProfits(ProfitsDataTable $dataTable)
    {
        return $dataTable->render('backend.transaction.profit');
    }

    public function list(ProfitDistributeDataTable $dataTable)
    {
        return $dataTable->render('backend.transaction.distribute');
    }

    public function index() {
        $current_datetime = Carbon::now()->timezone('Europe/Berlin')->format('Y-m-d h:i:s');
        return view('backend.profit.index', compact('current_datetime'));
    }

    public function push(Request $request) {
        $input = $request->all();

        $validator = Validator::make($input, [
            'amount' => 'required|regex:/^\d*(\.\d{2})?$/',
            'method' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first(), 'Error');
            return redirect()->back();
        }

        $total_profit = floatval($input['amount']);
        $method = boolval($input['method']);

        
        if ($method == true) { 
            $completedTaskList = AutoTask::where('type', AutoTaskType::ProfitShare)
                ->whereDate('created_at', Carbon::today())
                ->where('status', TxnStatus::Success)
                ->get();

            foreach ($completedTaskList as $task) {
                $details = json_decode($task->data);

                if ($details->method == 'auto') {
                    notify()->error('Automatic Profit Sharing was already executed on Today', 'Error');
                    return redirect()->back();
                }
            }

            $data = [
                'type' => AutoTaskType::ProfitShare,
                'status' => TxnStatus::Pending,
                'data' => json_encode([
                    'amount' => $total_profit,
                    'method' => 'auto',
                    'datetime' => Carbon::createFromTimeString('18:00')->timezone('Europe/Berlin'),                  
                ])
            ];

            $pendingTaskList = AutoTask::where('type', AutoTaskType::ProfitShare)
                ->whereDate('created_at', Carbon::today())
                ->where('status', TxnStatus::Pending)
                ->get();

            foreach ($pendingTaskList as $task) {
                $details = json_decode($task->data);

                if ($details->method == 'auto') {
                    $task->update($data);
                    notify()->success('Profit Sharing Setting is Updated Successfully', 'success');
                    return redirect()->back();
                }
            }

            $autoTask = AutoTask::create($data);
            notify()->success('Profit Sharing Setting is Created Successfully', 'success');

        } else {
            $data = [
                'type' => AutoTaskType::ProfitShare,
                'status' => TxnStatus::Pending,
                'data' => json_encode([
                    'amount' => $total_profit,
                    'method' => 'manual',
                    'datetime' => null,                  
                ])
            ];

            $autoTask = AutoTask::create($data);
            notify()->success('Profit Sharing Setting is Created Successfully', 'success');
        }
        
        return redirect()->back();
    }

    public function todayList(Request $request) {
        if ($request->ajax()) {
            $data = AutoTask::where('type', AutoTaskType::ProfitShare)->orderBy('id', 'desc')->get();

            return Datatables::of($data)
                ->addIndexColumn()
                ->editColumn('status', 'backend.profit.include.__txn_status')
                ->editColumn('amount', 'backend.profit.include.__txn_amount')
                ->editColumn('type', 'backend.profit.include.__txn_type')
                ->editColumn('method', 'backend.profit.include.__txn_method')
                ->editColumn('datetime', function ($task) {
                    return $task->updated_at->format('Y-m-d h:i:s');
                })
                ->rawColumns(['status', 'amount', 'type', 'method'])
                ->make(true);
        }
    }
}
