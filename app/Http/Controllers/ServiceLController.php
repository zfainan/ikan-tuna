<?php

namespace App\Http\Controllers;

use App\Models\ServiceL;
use App\Models\PenerimaanIkan;
use App\Models\Supplier;
use App\Models\Kategori_produk;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

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