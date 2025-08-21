<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDepositsRequest;
use App\Http\Requests\UpdateDepositsRequest;
use App\Models\Messages;
use App\Models\MenuRequests;
use App\DataTables\UssdSessionsDataTable;
use App\DataTables\MenuRequestsDataTable;

class UssdSessionsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(UssdSessionsDataTable $dataTable)
    {
        return $dataTable->render('pages/apps.ussd.list');
    }

    public function menu_requests(MenuRequestsDataTable $dataTable)
    {
        return $dataTable->render('pages/apps.ussd.menu');
    }

    public function menu_requests_show($id )
    {
            $menu_requests = MenuRequests::where('id', $id)->firstOrFail();
            return response()->json($menu_requests);
            //return view('pages/apps.ussd.show', compact('menu_requests'));
    }

}
