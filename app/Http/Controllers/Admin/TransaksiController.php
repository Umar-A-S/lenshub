<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class TransaksiController extends Controller
{
    public function index()
    {
        return view('admin.transaksi');
    }
}