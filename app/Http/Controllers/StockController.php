<?php

namespace App\Http\Controllers;

class StockController extends Controller

{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.transaksi.stock');
    }
}
