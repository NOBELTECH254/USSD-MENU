<?php

namespace App\DataTables;

use App\Models\AccountSignups;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AccountSignupsDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
        ->filter(function ($query) {
            if (request()->has('phone_number') && !empty(request('phone_number'))) {

                $query->where('mobile_number', 'like', "%" . request('phone_number') . "%");
            }
            /*
if (request()->has('date_range') && !empty(request('date_range'))) {
                [$start, $end] = explode(' - ', request('date_range'));
                $query->whereBetween('created_at', [
                    date('Y-m-d 00:00:00', strtotime($start)),
                    date('Y-m-d 23:59:59', strtotime($end))
                ]);
            }
            */
            if (request()->filled('start_date') && request()->filled('end_date')) {
                $query->whereBetween('created_at', [request('start_date'), request('end_date')]);
            }

        })
        ->editColumn('created_at', function (AccountSignups $model) {
            return $model->created_at->format('Y-m-d H:i:s');
        })
        ->editColumn('updated_at', function (AccountSignups $model) {
            return $model->created_at->format('Y-m-d H:i:s');
        })
            ->setRowId('id');
    }


    /**
     * Get the query source of dataTable.
     */
    public function query(AccountSignups $model): QueryBuilder
    {
        return $model->newQuery()->orderBy('id','desc');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('signup-table')
            ->columns($this->getColumns())
            ->minifiedAjax('', null, [
                'phone_number' => 'function() { return $("#phone_number").val(); }',
                'start_date'   => 'function() { return $("#start_date").val(); }',
                'end_date'     => 'function() { return $("#end_date").val(); }',
            ])
            ->addTableClass('table table-striped table-bordered table-hover')
            ->serverSide(true)
->processing(true)
->dom('Brtip') ->orderBy(2)->buttons([
    Button::make('excel')->filename('profiles_' . now()->format('Ymd_His')),
    Button::make('pdf')->filename('profiles_' . now()->format('Ymd_His')),
])   ->parameters([
  // 'scrollX' => true, // enables horizontal scroll
 //   'responsive' => true, // fit in container
])->orderBy(1);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('id')->searchable(false),
            Column::make('mobile_number'),
            Column::make('customer_name'),
            Column::make('year_of_birth'),
            Column::make('national_id'),
            Column::make('terms_approved'),
            Column::computed('created_at')->title('date sent')->addClass('text-nowrap'),
            Column::make('updated_at')->title('Last udate'),
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
        return 'signup_' . date('YmdHis');
    }
}
