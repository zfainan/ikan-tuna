<?php

namespace App\Http\Controllers;

use App\Models\CuttingL;

class CuttingLController extends Controller
{
    public function index()
    {
        $cuttingl = CuttingL::all();
        return view('admin.transaksi.cuttingl', [
            'cuttingl' => $cuttingl
        ]);
    }   
}