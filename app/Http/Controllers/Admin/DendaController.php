<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class DendaController extends Controller
{
    public function index()
    {
        return view('admin.denda');
    }
}