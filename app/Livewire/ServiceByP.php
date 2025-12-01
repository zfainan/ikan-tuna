<?php

namespace App\Livewire;

use App\Models\PenerimaanIkan;
use App\Models\KategoriByprodukCt;
use App\Models\Service;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ServiceByP extends Component
{

    // Properti untuk form input dan filter
    public $services = [];
    public $session_tgl_service;
    public $session_tgl_injek_co;
    public $total_berat = [];
    public $total_pcs = [];
    public $penerimaan_ikan;                    //tabel penerimaan
    public $penerimaan_id;
    public $selectedTanggalPenerimaan;
    public $filteredPenerimaan = [];
    public $suppliers = [];
    public $selectedSupplier;
    public $kategori_byproduk_ct = [];          //tabel produk
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
    public $groupedServices = [];

    // Property untuk filter
    public $filter_tgl_service_from;
    public $filter_tgl_service_to;
    public $filter_tgl_injek_co_from;
    public $filter_tgl_injek_co_to;
    public $filter_tgl_penerimaan_from;
    public $filter_tgl_penerimaan_to;
    public $filter_jenis_penerimaan;

    // Properties for editing
    public $service_id;
    public $edit_tgl_service;
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
        if (request()->has('tgl_service')) {
            $this->session_tgl_service = request('tgl_service');
        }
        if (request()->has('tgl_injek_co')) {
            $this->session_tgl_injek_co = request('tgl_injek_co');
        }
        if (request()->has('penerimaan_id')) {
            $this->penerimaan_id = request('penerimaan_id');
            $this->selectedTanggalPenerimaan = request('penerimaan_id');
        }

        // Load data jika semua filter terisi
        if ($this->session_tgl_service && $this->session_tgl_injek_co && $this->penerimaan_id) {
            $this->loadData();
        } else {
            $this->reset(['rows']);
            $this->addRow();
        }
    }

    // Fungsi untuk memuat data yang sudah ada di database
    public function loadData()
    {
        try {
            $this->rows = [];

            // Ambil data dari database
            $query = Service::with(['kategori_byproduk', 'penerimaan']);

            // Filter berdasarkan form input
            if ($this->penerimaan_id) {
                $query->where('penerimaan_id', $this->penerimaan_id);
            }

            if ($this->session_tgl_service) {
                $query->where('tgl_service', $this->session_tgl_service);
            }

            if ($this->session_tgl_injek_co) {
                $query->where('tgl_injek_co', $this->session_tgl_injek_co);
            }

            // Ambil data dan urutkan berdasarkan no_batch dan kategori
            $services = $query->orderBy('no_batch')
                ->orderBy('kategori_byproduk_id')
                ->get();

            // Kelompokkan data berdasarkan no_batch
            $groupedData = [];

            foreach ($services as $service) {
                $noBatch = $service->no_batch;

                if (!isset($groupedData[$noBatch])) {
                    $groupedData[$noBatch] = [
                        'no_batch' => $noBatch,
                        'tgl_service' => $service->tgl_service,
                        'tgl_injek_co' => $service->tgl_injek_co,
                        'produk' => []
                    ];
                }

                // Pastikan nilai berat dan total adalah single value, bukan array
                $berat = is_array($service->berat_produk) ? $service->berat_produk[0] : $service->berat_produk;
                $total = is_array($service->total_produk) ? $service->total_produk[0] : $service->total_produk;

                // Tambahkan data produk
                $groupedData[$noBatch]['produk'][] = [
                    'kategori_id' => $service->kategori_byproduk_id,
                    'nama' => $service->kategori_byproduk->nama_produk ?? 'Produk Tidak Diketahui',
                    'berat' => (float) $berat,
                    'total' => (int) $total
                ];
            }

            // Format data sesuai yang diharapkan view
            $formattedRows = [];

            foreach ($groupedData as $batch) {
                $row = [
                    'no_batch' => $batch['no_batch'],
                    'tgl_service' => $batch['tgl_service'],
                    'tgl_injek_co' => $batch['tgl_injek_co']
                ];

                // Inisialisasi semua kolom produk
                for ($i = 1; $i <= 7; $i++) {
                    $row['berat_produk' . $i] = null;
                    $row['total_produk' . $i] = null;
                    // Inisialisasi kategori yang dipilih
                    $this->selectedKategoriByproduk[$i] = null;
                }

                // Isi data produk
                foreach ($batch['produk'] as $index => $produk) {
                    $urutan = $index + 1;
                    if ($urutan <= 7) { // Maksimal 7 kolom produk
                        $row['berat_produk' . $urutan] = $produk['berat'];
                        $row['total_produk' . $urutan] = $produk['total'];

                        // Set selectedKategoriByproduk untuk dropdown
                        $this->selectedKategoriByproduk[$urutan] = $produk['kategori_id'];
                    }
                }

                $formattedRows[] = $row;
            }

            $this->rows = $formattedRows;

            // Jika tidak ada data, tambahkan baris kosong
            if (empty($this->rows)) {
                $this->addRow();
            }

            // Debug: Tampilkan data yang akan dikirim ke view
            Log::info('Data yang akan ditampilkan:', $this->rows);
        } catch (\Exception $e) {
            Log::error('Error loading data: ' . $e->getMessage());
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
    //memuat data- data yang ada pada penerimaan ikan

    //add, update, remove row

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
                    $deletRows = Service::where('no_batch', $no_batch)->delete();

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
                'session_tgl_service' => 'required|date',
                'session_tgl_injek_co' => 'required|date|after_or_equal:session_tgl_service',
            ], [
                'penerimaan_id.required' => 'Penerimaan harus dipilih',
                'session_tgl_service.required' => 'Tanggal service harus diisi',
                'session_tgl_injek_co.required' => 'Tanggal injek CO harus diisi',
                'session_tgl_injek_co.after_or_equal' => 'Tanggal injek CO harus setelah atau sama dengan tanggal service',
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
                throw new \Exception('Tidak ada data yang akan disimpan. Pastikan Anda telah mengisi minimal satu data produk.');
            }

            DB::beginTransaction();

            // Hapus data lama berdasarkan filter yang sama
            Service::where('tgl_service', $this->session_tgl_service)
                ->where('tgl_injek_co', $this->session_tgl_injek_co)
                ->where('penerimaan_id', $this->penerimaan_id)
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
                            Service::create([
                                'tgl_service' => $this->session_tgl_service,
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
                                'tgl_service' => $this->session_tgl_service,
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
                throw new \Exception('Tidak ada data yang berhasil disimpan. Pastikan Anda telah mengisi data dengan benar.');
            }

            DB::commit();

            session()->flash('message', 'Data berhasil disimpan');
            $this->loadData(); // Memuat ulang data setelah disimpan

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat menyimpan data service: ' . $e->getMessage());
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

    public function loadServiceForEdit($id)
    {
        $service = Service::findOrFail($id);
        if ($service) {
            $this->edit_tgl_service = $service->tgl_service;
            $this->edit_tgl_injek_co = $service->tgl_injek_co;
            $this->selectedSupplier = $service->supplier_id;
        }
    }

    public function updateService()
    {
        // Validasi input
        $this->validate([
            'edit_tgl_service' => 'required|date',
            'edit_tgl_injek_co' => 'required|date',
            'selectedSupplier' => 'required',
        ]);

        // Update data service
        $service = Service::findOrFail($this->service_id);
        $service->update([
            'tgl_service' => $this->edit_tgl_service,
            'tgl_injek_co' => $this->edit_tgl_injek_co,
        ]);

        // Refresh data setelah update
        $this->filterData();

        // Tampilkan pesan sukses
        session()->flash('message', 'Service data updated successfully.');
    }

    public function delete($id)
    {
        Service::destroy($id);
        $this->filterData();
    }

    // Method untuk mengambil,filter & reset data yang ada
    public function applyFilters()
    {
        $query = Service::query()
            ->join('penerimaan_ikan', 'service.penerimaan_id', '=', 'penerimaan_ikan.penerimaan_id')
            ->select('services.*');

        if ($this->filter_tgl_service_from) {
            $query->whereDate('services.tgl_service', '>=', $this->filter_tgl_service_from);
        }

        if ($this->filter_tgl_service_to) {
            $query->whereDate('services.tgl_service', '<=', $this->filter_tgl_service_to);
        }

        if ($this->filter_tgl_injek_co_from) {
            $query->whereDate('services.tgl_injek_co', '>=', $this->filter_tgl_injek_co_from);
        }

        if ($this->filter_tgl_injek_co_to) {
            $query->whereDate('services.tgl_injek_co', '<=', $this->filter_tgl_injek_co_to);
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

        return $query->orderBy('services.created_at', 'desc')->get();
    }

    public function resetFilters()
    {
        $this->filter_tgl_service_from = now()->format('Y-m-d');
        $this->filter_tgl_service_to = now()->format('Y-m-d');
        $this->filter_tgl_injek_co_from = now()->format('Y-m-d');
        $this->filter_tgl_injek_co_to = now()->format('Y-m-d');
        $this->filter_tgl_penerimaan_from = now()->format('Y-m-d');
        $this->filter_tgl_penerimaan_to = now()->format('Y-m-d');
        $this->filter_jenis_penerimaan = '';

        $this->applyFilters();
    }

    protected function getFilteredData()
    {
        $query = Service::with(['penerimaan_ikan.supplier']);

        //filter tanggal service
        if ($this->filter_tgl_service_from && $this->filter_tgl_service_to) {
            $query->whereBetween('tgl_service', [
                $this->filter_tgl_service_from . ' 00:00:00',
                $this->filter_tgl_service_to . ' 23:59:59'
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

        return $query->orderBy('tgl_service', 'desc')->get();
    }

    // Method untuk filter data
    public function filterData()
    {
        $query = Service::with(['penerimaan.supplier', 'kategoriByproduk']);

        // Filter berdasarkan tanggal service
        if ($this->session_tgl_service) {
            $query->whereDate('tgl_service', $this->session_tgl_service);
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

        $this->services = $query->orderBy('created_at', 'desc')->get();
    }

    public function render()
    {
        $this->filterData();

        return view('livewire.service', [
            'services' => $this->rows,
            'session_tgl_service' => $this->session_tgl_service,
            'session_tgl_injek_co' => $this->session_tgl_injek_co,
            'selectedSupplier' => $this->selectedSupplier,
            'penerimaan_ikan' => $this->penerimaan_ikan,
            'kategori_byproduk' => KategoriByprodukCt::all(),
            'groupedServices' => $this->groupServicesByProduct(), // Menambahkan data yang sudah dikelompokkan
            'produkList' => KategoriByprodukCt::all()
        ]);
    }

    // Method baru untuk mengelompokkan data service berdasarkan produk
    protected function groupServicesByProduct()
    {
        $grouped = [];

        foreach ($this->rows as $row) {
            // Pastikan row memiliki no_batch
            if (empty($row['no_batch'])) continue;

            $noBatch = $row['no_batch'];

            if (!isset($grouped[$noBatch])) {
                $grouped[$noBatch] = [
                    'no_batch' => $noBatch,
                    'tgl_service' => $row['tgl_service'] ?? null, // Gunakan null coalescing operator
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
        if ($this->session_tgl_service && $this->session_tgl_injek_co && $this->penerimaan_id) {
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

    // Update method updatedSessionTglService untuk reset data jika tanggal berubah
    public function updatedSessionTglService($value)
    {
        $this->reset(['session_tgl_injek_co', 'selectedTanggalPenerimaan', 'penerimaan_id', 'rows']);
        $this->addRow();
    }
}
