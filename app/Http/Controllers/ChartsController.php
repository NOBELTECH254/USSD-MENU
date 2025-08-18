<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ChartsController extends Controller
{
    //

    public function index(){

       // return view('pages.dashboards.charts');
        return view('pages.dashboards.charts')->layout('layouts.app');

    }
}
