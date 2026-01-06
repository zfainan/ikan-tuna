<?php

namespace App\Http\Controllers;

use App\Models\Packing;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $user = $request->user();

        if (empty($user)) {
            abort(403);
        }

        $totalProduk = Packing::whereNull('kategori_byproduk_id')
            ->selectRaw('SUM(berat_produk) as total_berat, SUM(total_produk) as total_pcs')
            ->first();

        $totalByProduk = Packing::whereNull('kategori_produk_id')
            ->selectRaw('SUM(berat_produk) as total_berat, SUM(total_produk) as total_pcs')
            ->first();

        $data = [
            'total_berat' => ($totalProduk->total_berat ?? 0) + ($totalByProduk->total_berat ?? 0),
            'total_pcs' => ($totalProduk->total_pcs ?? 0) + ($totalByProduk->total_pcs ?? 0)
        ];

        $isAdmin = $user?->role_id == 1 || $user?->role_id == 2;
        if ($isAdmin) {
            return view('admin.dashboard', $data);
        }

        return view('karyawan.dashboard', $data);
    }
}
