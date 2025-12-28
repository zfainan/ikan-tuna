<?php

namespace App\Http\Controllers;

use App\Models\PenerimaanIkan;
use App\Models\Supplier;
use App\Models\Grade;
use App\Models\KategoriBeratPenerimaan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PenerimaanIkanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = PenerimaanIkan::all();
        $totaldata = PenerimaanIkan::count();
        $suppliers = Supplier::all();
        $grades = Grade::all();
        $kategori_berat_penerimaans = KategoriBeratPenerimaan::all();

        return view('admin.transaksi.penerimaan_ikan', [
            'data' => $data, 
            'suppliers' => $suppliers, 
            'grades' => $grades, 
            'kategori_berat_penerimaans' => $kategori_berat_penerimaans, 
            'totaldata' => $totaldata
        ]);
    }

    public function ikanPdf(Request $request)
    {
        $date = $request->get('date');
        $tgl_bongkar = $request->get('tgl_bongkar');
        $supplier = $request->get('supplier');
        $jenis_penerimaan = $request->get('jenis_penerimaan');
        
        $supplier_name = 'Semua Supplier';
        if ($supplier) {
            $supplierModel = Supplier::where('supplier_id', $supplier)->first();
            $supplier_name = $supplierModel ? $supplierModel->nama_supplier : 'Supplier Tidak Ditemukan';
        }

        $query = PenerimaanIkan::with(['grade', 'kategoriBeratPenerimaan', 'supplier']);

        if ($date) {
            $query->whereDate('tgl_penerimaan', $date);
        }

        if ($tgl_bongkar) {
            $query->whereDate('tgl_bongkar', $tgl_bongkar);
        }

        if ($supplier) {
            $query->where('supplier_id', $supplier);
        }

        if ($jenis_penerimaan) {
            $query->where('jenis_penerimaan', $jenis_penerimaan);
        }

        $data = $query->get();
        $jenis_penerimaan_display = $jenis_penerimaan ?: 'Semua Jenis Penerimaan';

        $pdf = Pdf::loadView('pdf.ikan', compact('data', 'date', 'tgl_bongkar', 'supplier_name', 'jenis_penerimaan_display'));
        return $pdf->download('laporan_penerimaan_ikan_' . ($date ?? 'all_dates') . '.pdf');
    }

// Store Penerimaan Ikan
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'tgl_penerimaan' => 'required|date',                                        // Tabel Penerimaan Ikan
                'berat_ikan' => 'required|numeric|min:10',
                'supplier_id' => 'required|exists:suppliers,supplier_id',                   // Tabel supplier
                'grade_id' => 'required|integer|exists:grades,id',                                  // Tabel grade
                'kategori_berat_id' => 'required|exists:kategori_berat_penerimaans,id',     // Tabel Kategori Berat Penerimaan
            ]);

            $kategoriBeratId = $this->getKategoriBeratId($validated['berat_ikan']);

            if (!$kategoriBeratId) {
                return redirect()->back()->withErrors(['berat_ikan' => 'Kategori berat tidak dapat ditentukan untuk berat ikan ini.'])->withInput();
            }

            PenerimaanIkan::create([
                'tgl_penerimaan' => $validated['tgl_penerimaan'],                           // Tabel Penerimaan Ikan
                'berat_ikan' => $validated['berat_ikan'],
                'supplier_id' => $validated['supplier_id'],                                  // Tabel supplier
                'grade_id' => $validated['grade_id'],                                        // Tabel grade
                'kategori_berat_id' => $kategoriBeratId,                                    // Tabel Kategori Berat Penerimaan
            ]);

            return redirect()->route('penerimaan_ikan.index')->with('success', 'Penerimaan Ikan berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Gagal menyimpan data: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Mendapatkan ID kategori berat berdasarkan berat ikan.
     */
    private function getKategoriBeratId($berat)
    {
        if ($berat >= 20) {
            $kategori = KategoriBeratPenerimaan::where('kategori_berat', '20 UP')->first();
            return $kategori ? $kategori->id : null;
        } elseif ($berat >= 10 && $berat < 20) {
            $kategori = KategoriBeratPenerimaan::where('kategori_berat', '20 DOWN')->first();
            return $kategori ? $kategori->id : null;
        }

        throw new \Exception("Berat ikan harus minimal 10kg untuk dapat dikategorikan.");
    }

    /**
     * Display the specified resource.
     */
    public function show($penerimaan_id)
    {
        $penerimaanIkan = PenerimaanIkan::where('penerimaan_id', $penerimaan_id)->firstOrFail();
        return view('admin.transaksi.show_penerimaan_ikan', compact('penerimaanIkan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($penerimaan_id)
    {
        $penerimaanIkan = PenerimaanIkan::where('penerimaan_id', $penerimaan_id)->firstOrFail();
        return view('admin.transaksi.edit_penerimaan_ikan', compact('penerimaanIkan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $penerimaan_id)
    {
        $validated = $request->validate([
            'tgl_penerimaan' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,supplier_id',
            'grade_id' => 'required|exists:grades,id',
            'berat_ikan' => 'required|numeric|min:10',
        ]);

        $kategoriBeratId = $this->getKategoriBeratId($validated['berat_ikan']);

        $penerimaanIkan = PenerimaanIkan::where('penerimaan_id', $penerimaan_id)->firstOrFail();
        $penerimaanIkan->update([
            'tgl_penerimaan' => $validated['tgl_penerimaan'],
            'supplier_id' => $validated['supplier_id'],
            'grade_id' => $validated['grade_id'],
            'kategori_berat_id' => $kategoriBeratId,
            'berat_ikan' => $validated['berat_ikan'],
        ]);

        return redirect()->route('penerimaan_ikan.index')->with('success', 'Penerimaan Ikan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($penerimaan_id)
    {
        $penerimaanIkan = PenerimaanIkan::where('penerimaan_id', $penerimaan_id)->firstOrFail();
        $penerimaanIkan->delete();

        return redirect()->route('penerimaan_ikan.index')->with('success', 'Penerimaan Ikan berhasil dihapus.');
    }
}
