<?php

namespace App\DataTables;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

use App\Enums\TxnType;

class DepositHistoryDataTable extends DataTable
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
            ->addColumn('status', 'backend.transaction.include.__txn_status')
            ->addColumn('type', 'backend.transaction.include.__txn_type')
            ->addColumn('username', 'backend.transaction.include.__user')
            ->editColumn('final_amount', 'backend.transaction.include.__txn_amount')
            ->addColumn('first_name', function ($transaction) {
                return ($transaction->user->first_name);
            })
            ->addColumn('last_name', function ($transaction) {
                return ($transaction->user->last_name);
            })
            ->rawColumns(['status', 'type', 'username', 'final_amount']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Transaction $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Transaction $model): QueryBuilder
    {
        return $model::whereIn('type', [
            TxnType::ManualDeposit,
            TxnType::Deposit,
        ])->newQuery();
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
            Column::make('id'),
            Column::make('created_at')
                ->title('Date'),
            Column::computed('username'),
            Column::computed('first_name'),
            Column::computed('last_name'),
            Column::make('tnx'),
            Column::computed('type'),
            Column::make('method'),
            Column::computed('status')
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
