<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class SewaController extends Controller
{
    public function index()
    {
        return view('admin.sewa');
    }
}