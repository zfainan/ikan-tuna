<?php

namespace App\Http\Controllers;

use App\Models\Packing;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PackingReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __invoke()
    {
        return view('admin.laporan.packing');
    }

    public function print(Request $request)
    {
        $since = $request->get('since');
        $until = $request->get('until');

        $data = Packing::with([
            'kategoriByProduk',
            'kategoriProduk',
            'penerimaan'
        ])
            ->whereBetween('tanggal', [$since, $until])
            ->groupBy('tanggal')
            ->groupBy('kode_lot')
            ->groupBy('kategori_byproduk_id')
            ->groupBy('kategori_produk_id')
            ->selectRaw('kategori_produk_id, kategori_byproduk_id, kode_lot, tanggal, SUM(berat_produk) as total_berat_produk, SUM(total_produk) as total_produk')
            ->get();

        // return $data;

        $pdf = Pdf::loadView(
            'pdf.packing',
            compact(
                'data',
                'since',
                'until',
            )
        );
        $date = null;
        if ($since && $until) {
            $date = $since . '_to_' . $until;
        } elseif ($since) {
            $date = 'from_' . $since;
        } elseif ($until) {
            $date = 'up_to_' . $until;
        }
        return $pdf->download(
            'laporan_packing_' . ($date ?? 'all_dates') . '.pdf'
        );
    }
}
