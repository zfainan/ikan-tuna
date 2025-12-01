<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\PenerimaanIkan;
use App\Models\Supplier;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;


class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $services = Service::all();
        $penerimaan_ikan = PenerimaanIkan::all();
        $suppliers = Supplier::all();
        $selectedSupplier = null;

        return view('admin.transaksi.service', [
            'services' => $services,
            'penerimaan_ikan' => $penerimaan_ikan,
            'suppliers' => $suppliers,
            'selectedSupplier' => $selectedSupplier
        ]);
    }

    // PDF Service
    public function servicePdf(Request $request)
    {
        $filterMonth = $request->input('filterMonth');
    
        $services = Service::whereMonth('tgl_service', Carbon::parse($filterMonth)->month)
            ->whereYear('tgl_service', Carbon::parse($filterMonth)->year)
            ->with(['kategori_berat', 'penerimaan_ikan.supplier'])
            ->get();
    
        $pdf = Pdf::loadView('pdf.cutting', [
            'services' => $services,
            'filterMonth' => $filterMonth,
        ]);
    
        return $pdf->download('cutting_report_' . $filterMonth . '.pdf');
    }
    

    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'tgl_service' => 'required|date',
            'tgl_injek_co' => 'required|date',
            'berat_produk' => 'required|numeric|min:1',
            'total_produk' => 'required|numeric|min:1',
            'kategori_byproduk_id' => 'required',
            'supplier_id' => 'required',
            'selectedSupplier' => 'required',
        ]);
    
        // Simpan data service
        Service::create([
            'tgl_service' => $validated['tgl_service'],
            'tgl_injek_co' => $validated['tgl_injek_co'],
            'berat_produk' => $validated['berat_produk'],
            'total_produk' => $validated['total_produk'],                   
            'kategori_byproduk_id' => $validated['kategori_byproduk_id'],
            'supplier_id' => $validated['supplier_id'],
            'selectedSupplier' => $validated['selectedSupplier'],
        ]);
    
        return redirect()->route('service.index')->with('success', 'Service berhasil ditambahkan.');
    }

  
    public function update(Request $request, Service $service)
    {
        $data = [
            'id_produk' => $request->id_produk,
            'kategori_berat_id' => $request->kategori_berat_id,
            'berat_produk' => $request->berat_produk,
            'tgl_service' => $request->tgl_service,
            'tgl_injek_co' => $request->tgl_injek_co,
            'supplier_id' => $request->supplier_id,
            'selectedSupplier' => $request->selectedSupplier,
        ];

        $service->update($data);

        return redirect()->route('service.index')->with('success', 'Service berhasil diperbarui.');
    }
}
