<?php

namespace App\DataTables;

use App\Models\ResponseTemplates;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ResponseTemplatesDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
        ->editColumn('created_at', function (ResponseTemplates $model) {
            return $model->created_at->format('Y-m-d H:i:s');
        })
        ->editColumn('updated_at', function (ResponseTemplates $model) {
            return $model->created_at->format('Y-m-d H:i:s');
        })->addColumn('action', function ($model) {
            return '
                <a href="#"  data-id="'.$model->id.'" data-templatename="'.$model->name.'" data-template="'.$model->message.'" 
                   class="text-info m-2 update-status">
                   <span class="fas fa-pencil" aria-hidden="true"></span>
                </a>

                
            ';
        })
        ->rawColumns(['action'])

            ->setRowId('id');
    }


    /**
     * Get the query source of dataTable.
     */
    public function query(ResponseTemplates $model): QueryBuilder
    {
        return $model->newQuery()->orderBy('id','desc');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('response-templates-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->addTableClass('table table-striped table-bordered table-hover')
            ->orderBy(1);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('id')->searchable(false),
            Column::make('name'),
            Column::make('message'),
            Column::computed('created_at')->title('date joined')->addClass('text-nowrap'),
            Column::make('updated_at')->title('Last date'),

            Column::computed('action')
                ->addClass('text-end text-nowrap')
                ->exportable(false)
                ->printable(false)
                ->width(60) 
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
