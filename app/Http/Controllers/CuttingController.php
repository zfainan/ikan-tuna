<?php

namespace App\Http\Controllers;

use App\Models\Cutting;
use App\Models\PenerimaanIkan;
use App\Models\Supplier;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;


class CuttingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $penerimaan_ikan = PenerimaanIkan::all();
        $suppliers = Supplier::all();
        $selectedSupplier = null;

        return view('admin.transaksi.cutting', [
            'penerimaan_ikan' => $penerimaan_ikan,
            'suppliers' => $suppliers,
            'selectedSupplier' => $selectedSupplier
        ]);
    }

    // PDF Cutting
    public function cuttingPdf(Request $request)
    {
        $filterMonth = $request->input('filterMonth');
    
        $cuttings = Cutting::whereMonth('tgl_cutting', Carbon::parse($filterMonth)->month)
            ->whereYear('tgl_cutting', Carbon::parse($filterMonth)->year)
            ->with(['kategori_berat', 'penerimaan_ikan.supplier'])
            ->get();
    
        $pdf = Pdf::loadView('pdf.cutting', [
            'cuttings' => $cuttings,
            'filterMonth' => $filterMonth,
        ]);
    
        return $pdf->download('cutting_report_' . $filterMonth . '.pdf');
    }
    

    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'tgl_cutting' => 'required|date',               // Tabel Cutting
            'tgl_injek_co' => 'required|date',
            'berat_produk' => 'required|numeric|min:1',
            'total_produk' => 'required|numeric|min:1',
            'kategori_byproduk_id' => 'required',           // Tabel Kategori By Produk
            'supplier_id' => 'required',
            'selectedSupplier' => 'required',
        ]);
    
        // Tentukan kategori berat otomatis berdasarkan berat produk
        $kategoriBeratId = $this->getKategoriBeratId($validated['berat_produk']);
    
        // Simpan data cutting
        Cutting::create([
            'tgl_cutting' => $validated['tgl_cutting'],                     // Tabel Cutting
            'tgl_injek_co' => $validated['tgl_injek_co'],
            'berat_produk' => $validated['berat_produk'],
            'total_produk' => $validated['total_produk'],                   
            'kategori_byproduk_id' => $validated['kategori_byproduk_id'],   // Tabel Kategori By Produk
            'supplier_id' => $validated['supplier_id'],
            'selectedSupplier' => $validated['selectedSupplier'],
        ]);
    
        return redirect()->route('cutting.index')->with('success', 'Cutting berhasil ditambahkan.');
    }

  
    public function update(Request $request, Cutting $cutting)
    {

        $data = [
            'id_produk' => $request->id_produk,
            'kategori_berat_id' => $request->kategori_berat_id,
            'berat_produk' => $request->berat_produk,
            'tgl_cutting' => $request->tgl_cutting,
            'tgl_injek_co' => $request->tgl_injek_co,
            'supplier_id' => $request->supplier_id,
            'selectedSupplier' => $request->selectedSupplier,
        ];

        $cutting->update($data);

        return redirect()
            ->route('cutting.index')
            ->with('success', 'Cutting berhasil diperbarui.');
    }
}
