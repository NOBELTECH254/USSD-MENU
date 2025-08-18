<?php

namespace App\DataTables;

use App\Models\Profiles;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class ProfilesDataTable extends DataTable
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
        ->editColumn('last_request_at', function (Profiles $profile) {
            if($profile->last_request_at ==null)
            return "";
           return $profile->last_request_at->format('Y-m-d H:i:s');
        })
        ->editColumn('mobile_number', function (Profiles $profile) {
            return '<a href="'.route('profiles.show', $profile->id).'">'.$profile->mobile_number.'</a>';
        })
        ->editColumn('last_dial_at', function (Profiles $profile) {
            if($profile->last_dial_at ==null)
            return "";
            return $profile->last_dial_at->format('Y-m-d H:i:s');
        })
                ->editColumn('created_at', function (Profiles $profile) {
            return $profile->created_at->format('Y-m-d H:i:s');
        })
        ->editColumn('updated_at', function (Profiles $profile) {
            return $profile->updated_at->format('Y-m-d H:i:s');
        })
            ->addColumn('action', function (Profiles $profile) {
                return view('pages/apps.profiles._actions', compact('profile'));
            })
            ->rawColumns(['mobile_number', 'action'])
            ->setRowId('id');
    }


    /**
     * Get the query source of dataTable.
     */
    public function query(Profiles $model): QueryBuilder
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('profiles-table')
            ->columns($this->getColumns())
            ->minifiedAjax('', null, [
                'phone_number' => 'function() { return $("#phone_number").val(); }',
                'start_date'   => 'function() { return $("#start_date").val(); }',
                'end_date'     => 'function() { return $("#end_date").val(); }',
            ])
          // ->dom('rt' . "<'row'<'col-sm-12 col-md-5'l><'col-sm-12 col-md-7'p>>",)
            ->addTableClass('table table-striped table-bordered table-hover')
                        ->serverSide(true)
            ->processing(true)
            ->dom('Brtip') ->orderBy(2)->buttons([
                Button::make('excel')->filename('profiles_' . now()->format('Ymd_His')),
                Button::make('pdf')->filename('profiles_' . now()->format('Ymd_His')),
            ])   ->parameters([
              // 'scrollX' => true, // enables horizontal scroll
             //   'responsive' => true, // fit in container
            ]);;
    
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {

        
        return [
            Column::make('id')->searchable(false),
            Column::make('mobile_number')->title('mobile number'),
            Column::make('first_name')->title('first name'),
            Column::make('last_name')->title('last name'),
            Column::make('display_name')->addClass('d-flex align-items-center'),
            Column::make('customer_id'),
            Column::make('national_id')->searchable(false),
            Column::make('status'),
          //  Column::make('loan_requests')->searchable(false),
           // Column::make('payments_requests')->searchable(false),
            Column::computed('created_at')->title('date joined')->addClass('text-nowrap'),
            Column::make('updated_at')->title('Last update date'),
            Column::make('last_request_at')->title('Last Request date'),
            Column::make('last_dial_at')->title('Last Dial date'),
            Column::computed('action')
                ->addClass('text-end text-nowrap')
                ->width(60) 
        ];
    }
    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'profiles_' . now()->format('Ymd_His');
    }
}
