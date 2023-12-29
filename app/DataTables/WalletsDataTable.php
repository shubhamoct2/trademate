<?php

namespace App\DataTables;

use App\Models\User;
use App\Models\Wallet;
use App\Enums\WalletStatus;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class WalletsDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     * @return \Yajra\DataTables\EloquentDataTable
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('created', function ($wallet) {
                return $wallet->created_at->format('M d Y h:i');
            })
            ->editColumn('updated', function ($wallet) {
                return $wallet->updated_at->format('M d Y h:i');
            })
            ->editColumn('user', function ($wallet) {
                return $wallet->user->full_name;
            })
            ->editColumn('username', function ($wallet) {
                return $wallet->user->username;
            })
            ->addColumn('status', 'backend.wallet.include.__wallet_status')
            ->rawColumns(['status']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Wallet $model): QueryBuilder
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('dataTable')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->orderBy(1)
                    ->parameters([
                        'dom'          => 'Blfrtip',
                        'buttons'      => ['csv'],
                    ]);
    }

    /**
     * Get the dataTable columns definition.
     *
     * @return array
     */
    public function getColumns(): array
    {
        return [    
            // Column::computed('user'),
            Column::computed('username'),
            Column::make('currency'),
            Column::make('address'),
            Column::computed('status'),
            Column::computed('updated'),
            Column::computed('created'),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'Users_' . date('YmdHis');
    }
}
