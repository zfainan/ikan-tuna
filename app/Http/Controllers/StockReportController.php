<?php

namespace App\Http\Controllers;

use App\Models\KategoriByprodukCt;
use App\Models\KategoriProduk;
use App\Models\Packing;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class StockReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __invoke(Request $request)
    {
        $since = $request->get('since');
        $until = $request->get('until');

        $data = collect();

        KategoriByprodukCt::all()->each(function ($kategori) use (&$data, $since, $until) {
            $total = Packing::whereNull('kategori_produk_id')
                ->whereBetween('tanggal', [$since, $until] ?? [null, null])
                ->where('kategori_byproduk_id', $kategori->kategori_byproduk_id)
                ->selectRaw('SUM(berat_produk) as total_berat, SUM(total_produk) as total_pcs')
                ->first();

            $data->push([
                'product' => $kategori->nama_produk,
                'total_berat' => $total->total_berat ?? 0,
                'total_pcs' => $total->total_pcs ?? 0,
            ]);
        });

        KategoriProduk::all()->each(function ($kategori) use (&$data, $since, $until) {
            $total = Packing::whereNull('kategori_byproduk_id')
                ->whereBetween('tanggal', [$since, $until] ?? [null, null])
                ->where('kategori_produk_id', $kategori->kategori_byproduk_id)
                ->selectRaw('SUM(berat_produk) as total_berat, SUM(total_produk) as total_pcs')
                ->first();

            $data->push([
                'product' => $kategori->nama_produk,
                'total_berat' => $total->total_berat ?? 0,
                'total_pcs' => $total->total_pcs ?? 0,
            ]);
        });

        return view('admin.laporan.stock', compact('data', 'since', 'until'));
    }

    public function print(Request $request)
    {
        $since = $request->get('since');
        $until = $request->get('until');

        $data = collect();

        KategoriByprodukCt::all()->each(function ($kategori) use (&$data, $since, $until) {
            $total = Packing::whereNull('kategori_produk_id')
                ->whereBetween('tanggal', [$since, $until] ?? [null, null])
                ->where('kategori_byproduk_id', $kategori->kategori_byproduk_id)
                ->selectRaw('SUM(berat_produk) as total_berat, SUM(total_produk) as total_pcs')
                ->first();

            $data->push([
                'product' => $kategori->nama_produk,
                'total_berat' => $total->total_berat ?? 0,
                'total_pcs' => $total->total_pcs ?? 0,
            ]);
        });

        KategoriProduk::all()->each(function ($kategori) use (&$data, $since, $until) {
            $total = Packing::whereNull('kategori_byproduk_id')
                ->whereBetween('tanggal', [$since, $until] ?? [null, null])
                ->where('kategori_produk_id', $kategori->kategori_byproduk_id)
                ->selectRaw('SUM(berat_produk) as total_berat, SUM(total_produk) as total_pcs')
                ->first();

            $data->push([
                'product' => $kategori->nama_produk,
                'total_berat' => $total->total_berat ?? 0,
                'total_pcs' => $total->total_pcs ?? 0,
            ]);
        });

        $pdf = Pdf::loadView(
            'pdf.stock',
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
            'laporan_stock_' . ($date ?? 'all_dates') . '.pdf'
        );
    }
}
