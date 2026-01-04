<?php

namespace App\Http\Controllers;

use App\Models\Packing;

class PackingController extends Controller

{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.transaksi.packing', [
            'data' => Packing::all(),
        ]);
    }
}
