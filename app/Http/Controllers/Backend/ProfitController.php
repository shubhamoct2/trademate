<?php

namespace App\Http\Controllers\Backend;

use App\Enums\TxnType;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use DataTables;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\DataTables\ProfitsDataTable;

class ProfitController extends Controller
{
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
}
