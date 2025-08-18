<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDepositsRequest;
use App\Http\Requests\UpdateDepositsRequest;
use App\Models\Messages;
use App\DataTables\UssdSessionsDataTable;

class UssdSessionsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(UssdSessionsDataTable $dataTable)
    {
        return $dataTable->render('pages/apps.ussd.list');
    }

  
}
