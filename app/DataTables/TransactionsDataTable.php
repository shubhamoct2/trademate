<?php

namespace App\DataTables;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class TransactionsDataTable extends DataTable
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
            ->addColumn('method', function ($transaction) {
                if (!preg_match('/^[0-9]+$/', $transaction->method)) {
                    return $transaction->method;
                } else {
                    $method = intval($transaction->method);

                    $from = floor($method / 4);
                    $to = $method % 4;

                    $from_wallet = '';
                    if (0 == $from) {
                        $from_wallet = __('Main Wallet');
                    } else if (1 == $from) {
                        $from_wallet = __('Profit Wallet');
                    } else if (2 == $from) {
                        $from_wallet = __('Trading Wallet');
                    } else if (3 == $from) {
                        $from_wallet = __('Commission Wallet');
                    }

                    $to_wallet = '';
                    if (0 == $to) {
                        $to_wallet = __('Main Wallet');
                    } else if (1 == $to) {
                        $to_wallet = __('Profit Wallet');
                    } else if (2 == $to) {
                        $to_wallet = __('Trading Wallet');
                    } else if (2 == $to) {
                        $to_wallet = __('Commission Wallet');
                    }

                    return trans('translation.exchange_description', [
                        'from' => $from_wallet,
                        'to' => $to_wallet,
                    ]);
                }
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
            Column::make('id'),
            Column::make('created_at')
                ->title('Date'),
            Column::computed('username'),
            Column::computed('first_name'),
            Column::computed('last_name'),
            Column::make('tnx'),
            Column::computed('type'),
            Column::computed('final_amount'),
            Column::make('method'),
            Column::computed('status'),
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
