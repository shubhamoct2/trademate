<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use DataTables;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\DataTables\TransactionsDataTable;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function __construct()
    {
        $this->middleware('permission:transaction-list');

    }

    /**
     * @return Application|Factory|View|JsonResponse
     *
     * @throws \Exception
     */
    public function transactions(TransactionsDataTable $dataTable)
    {
        return $dataTable->render('backend.transaction.index');
    }

    public function delete_all_transaction() {
        Transaction::query()->delete();

        return redirect()->back();
    }
}
