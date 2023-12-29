<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DataTables\WalletsDataTable;

class WalletController extends Controller
{
    public function index(WalletsDataTable $dataTable) {
        return $dataTable->render('backend.wallet.index');
    }
}
