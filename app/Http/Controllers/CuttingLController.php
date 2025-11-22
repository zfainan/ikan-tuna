<?php

namespace App\Http\Controllers;

use App\Models\CuttingL;
use App\Models\PenerimaanIkan;
use App\Models\Supplier;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\GradeL;

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