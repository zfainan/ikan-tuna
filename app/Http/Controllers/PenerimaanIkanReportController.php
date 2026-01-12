<?php

namespace App\Http\Controllers;

use App\Models\PenerimaanIkan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PenerimaanIkanReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __invoke(Request $request)
    {
        $since = $request->get('since');
        $until = $request->get('until');

        $data = PenerimaanIkan::with([
            'supplier',
            'grade',
            'kategoriBeratPenerimaan'
        ])
            ->whereBetween('tgl_penerimaan', [$since, $until])
            ->groupBy('tgl_penerimaan')
            ->groupBy('supplier_id')
            ->groupBy('jenis_penerimaan')
            ->groupBy('grade_id')
            ->groupBy('kategori_berat_id')
            ->selectRaw('kategori_berat_id, grade_id, supplier_id, jenis_penerimaan, tgl_penerimaan, SUM(berat_ikan) as total_berat_ikan, COUNT(*) as jumlah_penerimaan')
            ->get();

        return view('admin.laporan.penerimaan_ikan', compact('data', 'since', 'until'));
    }

    public function print(Request $request)
    {
        $since = $request->get('since');
        $until = $request->get('until');

        $data = PenerimaanIkan::with([
            'supplier',
            'grade',
            'kategoriBeratPenerimaan'
        ])
            ->whereBetween('tgl_penerimaan', [$since, $until])
            ->groupBy('tgl_penerimaan')
            ->groupBy('supplier_id')
            ->groupBy('jenis_penerimaan')
            ->groupBy('grade_id')
            ->groupBy('kategori_berat_id')
            ->selectRaw('kategori_berat_id, grade_id, supplier_id, jenis_penerimaan, tgl_penerimaan, SUM(berat_ikan) as total_berat_ikan, COUNT(*) as jumlah_penerimaan')
            ->get();

        // return $data;

        $pdf = Pdf::loadView(
            'pdf.ikan',
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
            'laporan_penerimaan_ikan_' . ($date ?? 'all_dates') . '.pdf'
        );
    }
}
