<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDepositsRequest;
use App\Http\Requests\UpdateDepositsRequest;
use App\Models\ResponseTemplates;
use App\DataTables\ResponseTemplatesDataTable;

class ResponseTemplatesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ResponseTemplatesDataTable $dataTable)
    {
        return $dataTable->render('pages/apps.response-templates.list');
    }

  
}
