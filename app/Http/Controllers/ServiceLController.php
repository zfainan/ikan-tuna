<?php

namespace App\Http\Controllers;

use App\Models\ServiceL;

class ServiceLController extends Controller
{
    public function index()
    {
        $servicel = ServiceL::all();
        return view('admin.transaksi.servicel', [
            'servicel' => $servicel
        ]);
    }
}