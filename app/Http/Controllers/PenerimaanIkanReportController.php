<?php

namespace App\Http\Controllers;

use App\Models\PenerimaanIkan;
use App\Models\Supplier;
use App\Models\Grade;
use App\Models\KategoriBeratPenerimaan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PenerimaanIkanReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __invoke()
    {
        return view('admin.laporan.penerimaan_ikan');
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
