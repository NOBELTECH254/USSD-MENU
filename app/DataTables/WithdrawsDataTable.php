<?php

namespace App\DataTables;

use App\Models\Withdraws;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Relations\HasOne;

class WithdrawsDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
        ->editColumn('created_at', function (Withdraws $user) {
            return $user->created_at->format('Y-m-d H:i:s');
        })
        ->editColumn('updated_at', function (Withdraws $user) {
            return $user->created_at->format('Y-m-d H:i:s');
        })
          /*  ->addColumn('action', function (PlayerAccounts $user) {
                return view('pages/apps.user-management.users.columns._actions', compact('user'));
            })*/
            ->setRowId('id');
    }


    /**
     * Get the query source of dataTable.
     */
    public function query(Withdraws $model): QueryBuilder
    {
        return $model->newQuery()->with("playerAccounts")->orderBy('id','desc');;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('withdraws-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('rt' . "<'row'<'col-sm-12 col-md-5'l><'col-sm-12 col-md-7'p>>",)
            ->addTableClass('table align-middle table-row-dashed fs-6 gy-5 dataTable no-footer text-gray-600 fw-semibold')
            ->setTableHeadClass('text-start text-muted fw-bold fs-7 text-uppercase gs-0')
            ->orderBy(2);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('id')->searchable(false),
            Column::make('player_id'),
            [
                'name' => 'player_accounts.msisdn',
                'data' => 'player_accounts.msisdn',
                'title' => 'msisdn',
                'defaultContent' => '',
                'orderable' => 'false',
                'searchable'=>false
            ],
            Column::make('customer_name'),
            Column::make('msisdn'),
            Column::make('amount'),
            Column::make('tax')->searchable(false),
            Column::make('payer_transaction_id')->title('receipt number'),
            Column::make('status'),
            Column::computed('created_at')->title('date joined')->addClass('text-nowrap'),
            Column::make('updated_at')->title('Last date'),
/*
            Column::computed('action')
                ->addClass('text-end text-nowrap')
                ->exportable(false)
                ->printable(false)
                ->width(60) */
        ];
    }
    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'players_' . date('YmdHis');
    }
}
