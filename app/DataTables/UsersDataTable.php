<?php

namespace App\DataTables;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class UsersDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     * @return \Yajra\DataTables\EloquentDataTable
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        // total trading wallet balance
        $total_trading = User::where('status', 1)->sum('trading_balance');

        return (new EloquentDataTable($query))
            ->addColumn('select_users', '<input type="checkbox" name="selected_users[]" value="{{ $id }}" >')
            ->addColumn('avatar', 'backend.user.include.__avatar')
            ->addColumn('kyc', 'backend.user.include.__kyc')
            ->addColumn('status', 'backend.user.include.__status')
            ->addColumn('action', 'backend.user.include.__action')
            ->editColumn('shares', function ($user) use ($total_trading) {
                return number_format((floatval($user->trading_balance) * 100.00) / floatval($total_trading), 2);
            })
            ->rawColumns(['select_users', 'avatar', 'kyc', 'status', 'action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(User $model): QueryBuilder
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
            Column::computed('select_users')
                ->title('')
                ->exportable(false)
                ->printable(false)
                ->width('10px'),
            Column::make('id'),
            Column::computed('avatar')
                  ->exportable(false)
                  ->printable(false),
            Column::make('username'),
            Column::make('first_name'),
            Column::make('last_name'),
            Column::make('email'),
            Column::make('balance')
                ->title('Balance (' . setting('site_currency')  .')')
                ->orderable(true),
            Column::computed('total_profit')
                ->title('Total Profit (' . setting('site_currency')  .')')
                ->orderable(true),
            Column::computed('shares')
                ->title('Shares (%)')
                ->orderable(false),
            Column::computed('kyc'),
            Column::computed('status'),
            Column::computed('action')
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
