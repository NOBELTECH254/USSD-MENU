<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDepositsRequest;
use App\Http\Requests\UpdateDepositsRequest;
use App\Models\Messages;
use App\DataTables\MessagesDataTable;

class MessagesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(MessagesDataTable $dataTable)
    {
        return $dataTable->render('pages/apps.messages.list');
    }

  
}
