<?php

namespace App\Livewire;

use App\Models\Cutting;
use App\Models\PenerimaanIkan;
use App\Models\KategoriByprodukCt;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CuttingByP extends Component
{
    public $cuttings = [];
    public $session_tgl_cutting;
    public $session_tgl_injek_co;
    public $total_berat = [];
    public $total_pcs = [];
    public $penerimaan_ikan;
    public $penerimaan_id;
    public $selectedTanggalPenerimaan;
    public $filteredPenerimaan = [];
    public $suppliers = [];
    public $selectedSupplier;
    public $kategori_byproduk_ct = [];
    public $selectedKategoriByproduk = [
        1 => null,
        2 => null,
        3 => null,
        4 => null,
        5 => null,
        6 => null,
        7 => null,
    ];
    public $rows = [];
    public $data = [];
    public $groupedCuttings = [];

    // Property untuk filter
    public $filter_tgl_cutting_from;
    public $filter_tgl_cutting_to;
    public $filter_tgl_injek_co_from;
    public $filter_tgl_injek_co_to;
    public $filter_tgl_penerimaan_from;
    public $filter_tgl_penerimaan_to;
    public $filter_jenis_penerimaan;

    // Properties for editing
    public $cutting_id;
    public $edit_tgl_cutting;
    public $edit_tgl_injek_co;

    // Insialisasi data
    public function mount()
    {
        // Inisialisasi variabel yang diperlukan
        $this->kategori_byproduk_ct = KategoriByprodukCt::all();
        $this->penerimaan_ikan = PenerimaanIkan::with('supplier')
            ->orderBy('tgl_penerimaan', 'desc')
            ->get();

        $this->selectedKategoriByproduk = [
            1 => null,
            2 => null,
            3 => null,
            4 => null,
            5 => null,
            6 => null,
            7 => null
        ];

        $this->rows = [];
        $this->filteredPenerimaan = collect();
        $this->addRow();

        // Inisialisasi session jika ada di URL
        if (request()->has('tgl_cutting')) {
            $this->session_tgl_cutting = request('tgl_cutting');
        }
        if (request()->has('tgl_injek_co')) {
            $this->session_tgl_injek_co = request('tgl_injek_co');
        }
        if (request()->has('penerimaan_id')) {
            $this->penerimaan_id = request('penerimaan_id');
            $this->selectedTanggalPenerimaan = request('penerimaan_id');
        }

        // Load data jika semua filter terisi
        if ($this->session_tgl_cutting && $this->session_tgl_injek_co && $this->penerimaan_id) {
            $this->loadData();
        } else {
            $this->reset(['rows']);
            $this->addRow();
        }
    }

    // Fungsi untuk memuat data yang sudah ada di database
    public function loadData(bool $withKategoriData = false)
    {
        try {
            $this->rows = [];

            // Ambil data dari database
            $query = Cutting::with(['kategori_byproduk', 'penerimaan']);

            // Filter berdasarkan form input
            if ($this->penerimaan_id) {
                $query->where('penerimaan_id', $this->penerimaan_id);
            }

            if ($this->session_tgl_cutting) {
                $query->where('tgl_cutting', $this->session_tgl_cutting);
            }

            if ($this->session_tgl_injek_co) {
                $query->where('tgl_injek_co', $this->session_tgl_injek_co);
            }

            if ($withKategoriData) {
                $query->whereIn('kategori_byproduk_id', array_values(
                    array_filter($this->selectedKategoriByproduk)
                ));
            }

            // Ambil data dan urutkan berdasarkan no_batch dan kategori
            $cuttings = $query->orderBy('no_batch')
                ->orderBy('kategori_byproduk_id')
                ->get();

            // Kelompokkan data berdasarkan no_batch
            $groupedData = [];

            foreach ($cuttings as $cutting) {
                $noBatch = $cutting->no_batch;

                if (empty($groupedData[$noBatch])) {
                    $groupedData[$noBatch] = [];
                }

                if (empty($groupedData[$noBatch][$cutting->kategori_byproduk_id])) {
                    $groupedData[$noBatch][$cutting->kategori_byproduk_id] = [];
                }

                $groupedData[$noBatch][$cutting->kategori_byproduk_id][] = [
                    'kategori_id' => $cutting->kategori_byproduk_id,
                    'nama' => $cutting->kategori_byproduk->nama_produk ?? 'Produk Tidak Diketahui',
                    'berat' => is_array($cutting->berat_produk) ? (float)$cutting->berat_produk[0] : (float)$cutting->berat_produk,
                    'total' => is_array($cutting->total_produk) ? (int)$cutting->total_produk[0] : (int)$cutting->total_produk,
                ];
            }

            // Format data sesuai yang diharapkan view
            $formattedRows = [];

            foreach ($groupedData as $batch => $value) {
                $maxRows = max(array_map('count', $value));

                $rows = [];
                for ($i = 0; $i < $maxRows; $i++) {
                    $row = [
                        'no_batch' => $batch,
                        'tgl_cutting' => '',
                        'tgl_injek_co' => ''
                    ];

                    // Inisialisasi semua kolom produk
                    for ($j = 1; $j <= 7; $j++) {
                        $kategoriId = $this->selectedKategoriByproduk[$j] ?? null;

                        if (empty($kategoriId) || empty($value[$kategoriId][$i])) {
                            $row['berat_produk' . $j] = null;
                            $row['total_produk' . $j] = null;

                            continue;
                        }

                        $produk = $value[$kategoriId][$i];

                        $row['berat_produk' . $j] = $produk['berat'];
                        $row['total_produk' . $j] = $produk['total'];
                    }

                    $rows[] = $row;
                }

                $formattedRows = array_merge($formattedRows, $rows);
            }

            $this->rows = $formattedRows;

            // Jika tidak ada data, tambahkan baris kosong
            if (empty($this->rows)) {
                $this->addRow();
            }

            $this->calculateTotals();
        } catch (\Exception $e) {
            Log::error('Error loading data: ' . $e->getMessage());
            Log::error('Error loading data: ' . $e->getTraceAsString());
            session()->flash('error', 'Gagal memuat data: ' . $e->getMessage());
        }
    }

    // memuat data- data yang ada pada penerimaan ikan
    public function loadPenerimaanIkan()
    {
        $this->penerimaan_ikan = PenerimaanIkan::with('supplier')
            ->orderBy('tgl_penerimaan', 'desc')
            ->get();
        return $this->penerimaan_ikan;
    }

    public function updateSelectedTanggalPenerimaan($value)
    {
        if ($value) {
            $this->filteredPenerimaan = PenerimaanIkan::where('penerimaan_id', $value)
                ->with('supplier')
                ->get();
        } else {
            $this->filteredPenerimaan = collect();
        }
        $this->penerimaan_id = null;
    }

    public function addRow()
    {
        $newRow = ['no_batch' => ''];
        for ($i = 1; $i <= 7; $i++) {
            $newRow['berat_produk' . $i] = '';
            $newRow['total_produk' . $i] = '';
        }
        $this->rows[] = $newRow;
        $this->calculateTotals();
    }

    public function calculateTotals()
    {
        for ($i = 1; $i <= 7; $i++) {
            $this->total_berat[$i] = 0;
            $this->total_pcs[$i] = 0;
        }
        foreach ($this->rows as $row) {
            for ($i = 1; $i <= 7; $i++) {
                $this->total_berat[$i] += (float) ($row['berat_produk' . $i] ?? 0);
                $this->total_pcs[$i] += (int) ($row['total_produk' . $i] ?? 0);
            }
        }
    }

    public function update($propertyName)
    {
        Log::info('Update Property:', [
            'propertyName' => $propertyName,
            'rows' => $this->rows,
        ]);

        if (str_starts_with($propertyName, 'rows.')) {
            $this->calculateTotals();
        }
    }

    public function removeRow($index)
    {
        try {
            if (isset($this->rows[$index])) {
                $no_batch = $this->rows[$index]['no_batch'] ?? null;
                if ($no_batch) {
                    $deletRows = Cutting::where('no_batch', $no_batch)->delete();

                    if ($deletRows > 0) {
                        unset($this->rows[$index]);
                        $this->rows = array_values($this->rows);
                        session()->flash('message', 'Data berhasil dihapus');

                        $this->loadData();
                    }
                }
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus data: ' . $e->getMessage());
            return;
        }
    }

    public function saveAll()
    {
        try {
            // Validasi input
            $this->validate([
                'penerimaan_id' => 'required',
                'session_tgl_cutting' => 'required|date',
                'session_tgl_injek_co' => 'required|date|after_or_equal:session_tgl_cutting',
            ], [
                'penerimaan_id.required' => 'Penerimaan harus dipilih',
                'session_tgl_cutting.required' => 'Tanggal cutting harus diisi',
                'session_tgl_injek_co.required' => 'Tanggal injek CO harus diisi',
                'session_tgl_injek_co.after_or_equal' => 'Tanggal injek CO harus setelah atau sama dengan tanggal cutting',
            ]);

            // Validasi minimal satu data terisi
            $hasValidData = false;
            foreach ($this->rows as $row) {
                if (empty($row['no_batch'])) continue;

                for ($i = 1; $i <= 7; $i++) {
                    $berat = $row['berat_produk' . $i] ?? 0;
                    $total = $row['total_produk' . $i] ?? 0;
                    $kategoriId = $this->selectedKategoriByproduk[$i] ?? null;

                    if (($berat > 0 || $total > 0) && !empty($kategoriId)) {
                        $hasValidData = true;
                        break 2;
                    }
                }
            }

            if (!$hasValidData) {
                throw new \Exception(
                    'Tidak ada data yang akan disimpan. Pastikan Anda telah mengisi minimal satu data produk.'
                );
            }

            DB::beginTransaction();

            // Hapus data lama berdasarkan filter yang sama
            Cutting::where('tgl_cutting', $this->session_tgl_cutting)
                ->where('tgl_injek_co', $this->session_tgl_injek_co)
                ->where('penerimaan_id', $this->penerimaan_id)
                ->whereIn('kategori_byproduk_id', array_values(
                    array_filter($this->selectedKategoriByproduk)
                ))
                ->delete();

            $savedCount = 0;

            // Simpan data baru
            foreach ($this->rows as $row) {
                $noBatch = $row['no_batch'] ?? null;

                if (empty($noBatch)) {
                    continue; // Lewati jika no_batch kosong
                }

                // Proses setiap kolom produk (1-7)
                for ($i = 1; $i <= 7; $i++) {
                    $berat = (float)($row['berat_produk' . $i] ?? 0);
                    $total = (int)($row['total_produk' . $i] ?? 0);
                    $kategoriId = $this->selectedKategoriByproduk[$i] ?? null;

                    // Hanya simpan jika ada kategori, no batch, dan minimal satu nilai diisi
                    if (!empty($kategoriId) && !empty($noBatch) && ($berat > 0 || $total > 0)) {
                        try {
                            Cutting::create([
                                'tgl_cutting' => $this->session_tgl_cutting,
                                'tgl_injek_co' => $this->session_tgl_injek_co,
                                'penerimaan_id' => $this->penerimaan_id,
                                'kategori_byproduk_id' => $kategoriId,
                                'no_batch' => $noBatch,
                                'berat_produk' => [$berat],
                                'total_produk' => [$total],
                                'urutan_produk' => $i
                            ]);
                            $savedCount++;
                        } catch (\Exception $e) {
                            Log::error('Gagal menyimpan data: ' . $e->getMessage() . ' - Data: ' . json_encode([
                                'tgl_cutting' => $this->session_tgl_cutting,
                                'tgl_injek_co' => $this->session_tgl_injek_co,
                                'penerimaan_id' => $this->penerimaan_id,
                                'kategori_byproduk_id' => $kategoriId,
                                'no_batch' => $noBatch,
                                'berat' => $berat,
                                'total' => $total,
                                'urutan' => $i
                            ]));
                            throw $e;
                        }
                    }
                }
            }

            if ($savedCount === 0) {
                throw new \Exception(
                    'Tidak ada data yang berhasil disimpan. Pastikan Anda telah mengisi data dengan benar.'
                );
            }

            DB::commit();

            session()->flash('message', 'Data berhasil disimpan');
            $this->loadData();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat menyimpan data cutting: ' . $e->getMessage());
            session()->flash('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    protected function resetForm()
    {
        $this->rows = [];
        $this->addRow();
        $this->penerimaan_id = null;
        $this->selectedKategoriByproduk = [
            1 => null,
            2 => null,
            3 => null,
            4 => null,
            5 => null,
            6 => null,
            7 => null
        ];
    }

    public function loadCuttingForEdit($id)
    {
        $cutting = Cutting::findOrFail($id);
        if ($cutting) {
            $this->edit_tgl_cutting = $cutting->tgl_cutting;
            $this->edit_tgl_injek_co = $cutting->tgl_injek_co;
            $this->selectedSupplier = $cutting->supplier_id;
        }
    }

    public function updateCutting()
    {
        // Validasi input
        $this->validate([
            'edit_tgl_cutting' => 'required|date',
            'edit_tgl_injek_co' => 'required|date',
            'selectedSupplier' => 'required',
        ]);

        // Update data cutting
        $cutting = Cutting::findOrFail($this->cutting_id);
        $cutting->update([
            'tgl_cutting' => $this->edit_tgl_cutting,
            'tgl_injek_co' => $this->edit_tgl_injek_co,
        ]);

        // Refresh data setelah update
        $this->filterData();

        // Tampilkan pesan sukses
        session()->flash('message', 'Cutting data updated successfully.');
    }

    public function delete($id)
    {
        Cutting::destroy($id);
        $this->filterData();
    }

    // Method untuk mengambil,filter & reset data yang ada
    public function applyFilters()
    {
        $query = Cutting::query()
            ->join('penerimaan_ikan', 'cutting.penerimaan_id', '=', 'penerimaan_ikan.penerimaan_id')
            ->select('cuttings.*');

        if ($this->filter_tgl_cutting_from) {
            $query->whereDate('cuttings.tgl_cutting', '>=', $this->filter_tgl_cutting_from);
        }

        if ($this->filter_tgl_cutting_to) {
            $query->whereDate('cuttings.tgl_cutting', '<=', $this->filter_tgl_cutting_to);
        }

        if ($this->filter_tgl_injek_co_from) {
            $query->whereDate('cuttings.tgl_injek_co', '>=', $this->filter_tgl_injek_co_from);
        }

        if ($this->filter_tgl_injek_co_to) {
            $query->whereDate('cuttings.tgl_injek_co', '<=', $this->filter_tgl_injek_co_to);
        }

        if ($this->filter_tgl_penerimaan_from) {
            $query->whereDate('penerimaan_ikan.tgl_penerimaan', '>=', $this->filter_tgl_penerimaan_from);
        }

        if ($this->filter_tgl_penerimaan_to) {
            $query->whereDate('penerimaan_ikan.tgl_penerimaan', '<=', $this->filter_tgl_penerimaan_to);
        }

        if ($this->filter_jenis_penerimaan) {
            $query->where('penerimaan_ikan.jenis_penerimaan', $this->filter_jenis_penerimaan);
        }

        return $query->orderBy('cuttings.created_at', 'desc')->get();
    }

    public function resetFilters()
    {
        $this->filter_tgl_cutting_from = now()->format('Y-m-d');
        $this->filter_tgl_cutting_to = now()->format('Y-m-d');
        $this->filter_tgl_injek_co_from = now()->format('Y-m-d');
        $this->filter_tgl_injek_co_to = now()->format('Y-m-d');
        $this->filter_tgl_penerimaan_from = now()->format('Y-m-d');
        $this->filter_tgl_penerimaan_to = now()->format('Y-m-d');
        $this->filter_jenis_penerimaan = '';

        $this->applyFilters();
    }

    protected function getFilteredData()
    {
        $query = Cutting::with(['penerimaan_ikan.supplier']);

        //filter tanggal cutting
        if ($this->filter_tgl_cutting_from && $this->filter_tgl_cutting_to) {
            $query->whereBetween('tgl_cutting', [
                $this->filter_tgl_cutting_from . ' 00:00:00',
                $this->filter_tgl_cutting_to . ' 23:59:59'
            ]);
        }
        //filter tanggal injek co
        if ($this->filter_tgl_injek_co_from && $this->filter_tgl_injek_co_to) {
            $query->whereBetween('tgl_injek_co', [
                $this->filter_tgl_injek_co_from . ' 00:00:00',
                $this->filter_tgl_injek_co_to . ' 23:59:59'
            ]);
        }
        //filter tanggal penerimaan
        if ($this->filter_tgl_penerimaan_from && $this->filter_tgl_penerimaan_to) {
            $query->whereBetween('penerimaan_ikan.tgl_penerimaan', [
                $this->filter_tgl_penerimaan_from . ' 00:00:00',
                $this->filter_tgl_penerimaan_to . ' 23:59:59'
            ]);

            if ($this->filter_jenis_penerimaan) {
                $query->where('penerimaan_ikan.jenis_penerimaan', $this->filter_jenis_penerimaan);
            }
        }

        return $query->orderBy('tgl_cutting', 'desc')->get();
    }

    // Method untuk filter data
    public function filterData()
    {
        $query = Cutting::with(['penerimaan.supplier', 'kategoriByproduk']);

        // Filter berdasarkan tanggal cutting
        if ($this->session_tgl_cutting) {
            $query->whereDate('tgl_cutting', $this->session_tgl_cutting);
        }

        // Filter berdasarkan tanggal injek co
        if ($this->session_tgl_injek_co) {
            $query->whereDate('tgl_injek_co', $this->session_tgl_injek_co);
        }

        // Filter berdasarkan tanggal penerimaan
        if ($this->selectedTanggalPenerimaan) {
            $penerimaanIds = PenerimaanIkan::where('penerimaan_id', $this->selectedTanggalPenerimaan)
                ->pluck('penerimaan_id');
            $query->whereIn('penerimaan_id', $penerimaanIds);
        }

        // Filter berdasarkan jenis penerimaan
        if ($this->penerimaan_id) {
            $query->where('penerimaan_id', $this->penerimaan_id);
        }

        $this->cuttings = $query->orderBy('created_at', 'desc')->get();
    }

    public function render()
    {
        $this->filterData();

        return view('livewire.cutting', [
            'cuttings' => $this->rows,
            'session_tgl_cutting' => $this->session_tgl_cutting,
            'session_tgl_injek_co' => $this->session_tgl_injek_co,
            'selectedSupplier' => $this->selectedSupplier,
            'penerimaan_ikan' => $this->penerimaan_ikan,
            'kategori_byproduk' => KategoriByprodukCt::all(),
            'groupedCuttings' => $this->groupCuttingsByProduct(), // Menambahkan data yang sudah dikelompokkan
            'produkList' => KategoriByprodukCt::all()
        ]);
    }

    // Method baru untuk mengelompokkan data cutting berdasarkan produk
    protected function groupCuttingsByProduct()
    {
        $grouped = [];

        foreach ($this->rows as $row) {
            // Pastikan row memiliki no_batch
            if (empty($row['no_batch'])) continue;

            $noBatch = $row['no_batch'];

            if (!isset($grouped[$noBatch])) {
                $grouped[$noBatch] = [
                    'no_batch' => $noBatch,
                    'tgl_cutting' => $row['tgl_cutting'] ?? null, // Gunakan null coalescing operator
                    'tgl_injek_co' => $row['tgl_injek_co'] ?? null, // Gunakan null coalescing operator
                    'produk' => []
                ];
            }

            // Tambahkan produk yang memiliki nilai
            for ($i = 1; $i <= 7; $i++) {
                $beratKey = 'berat_produk' . $i;
                $totalKey = 'total_produk' . $i;

                // Cek apakah ada nilai yang akan ditambahkan
                $hasBerat = isset($row[$beratKey]) && $row[$beratKey] !== null;
                $hasTotal = isset($row[$totalKey]) && $row[$totalKey] !== null;

                if ($hasBerat || $hasTotal) {
                    $kategoriId = $this->selectedKategoriByproduk[$i] ?? null;

                    // Cari apakah produk dengan kategori_id yang sama sudah ada
                    $existingIndex = array_search(
                        $kategoriId,
                        array_column($grouped[$noBatch]['produk'], 'kategori_id')
                    );

                    if ($existingIndex !== false) {
                        // Update data yang sudah ada
                        if ($hasBerat) {
                            $grouped[$noBatch]['produk'][$existingIndex]['berat'] += (float)$row[$beratKey];
                        }
                        if ($hasTotal) {
                            $grouped[$noBatch]['produk'][$existingIndex]['total'] += (int)$row[$totalKey];
                        }
                    } else if ($kategoriId) {
                        // Tambahkan data baru
                        $grouped[$noBatch]['produk'][] = [
                            'kategori_id' => $kategoriId,
                            'nama' => $this->getProductName($kategoriId),
                            'berat' => $hasBerat ? (float)$row[$beratKey] : 0,
                            'total' => $hasTotal ? (int)$row[$totalKey] : 0
                        ];
                    }
                }
            }
        }

        return $grouped;
    }

    // Method untuk mendapatkan nama produk berdasarkan ID
    protected function getProductName($kategoriId)
    {
        if (!$kategoriId) return 'Produk Tidak Diketahui';

        $product = KategoriByprodukCt::find($kategoriId);
        return $product ? $product->nama_produk : 'Produk Tidak Diketahui';
    }

    // Update method updatedPenerimaanId untuk memuat data saat penerimaan_id berubah
    public function updatedPenerimaanId($value)
    {
        if ($this->session_tgl_cutting && $this->session_tgl_injek_co && $this->penerimaan_id) {
            $this->loadData();
        } else {
            $this->reset(['rows']);
            $this->addRow();
        }
    }

    // Update method updatedSessionTglInjekCo untuk reset data jika tanggal berubah
    public function updatedSessionTglInjekCo($value)
    {
        $this->reset(['selectedTanggalPenerimaan', 'penerimaan_id', 'rows']);
        $this->addRow();
    }

    // Update method updatedSessionTglCutting untuk reset data jika tanggal berubah
    public function updatedSessionTglCutting($value)
    {
        $this->reset(['session_tgl_injek_co', 'selectedTanggalPenerimaan', 'penerimaan_id', 'rows']);
        $this->addRow();
    }

    public function print()
    {
        $pdf = Pdf::loadView(
            'pdf.cutting_by_p',
            [
                'tgl_cutting' => $this->session_tgl_cutting,
                'tgl_injek_co' => $this->session_tgl_injek_co,
                'jenis_penerimaan' => PenerimaanIkan::find($this->penerimaan_id)->jenis_penerimaan ?? 'N/A',
                'selectedKategoriByproduk' => $this->selectedKategoriByproduk,
                'data' => $this->rows,
                'kategori_byproduk_ct' => $this->kategori_byproduk_ct,
                'total_berat' => $this->total_berat,
                'total_pcs' => $this->total_pcs,
            ]
        );
        $pdf->setPaper('A4', 'landscape');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            'cutting_by_p_' . now()->format('Ymd_His') . '.pdf'
        );
    }
}
