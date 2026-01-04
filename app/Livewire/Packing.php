<?php

namespace App\Livewire;

use App\Models\Cutting;
use App\Models\PenerimaanIkan;
use App\Models\KategoriByprodukCt;
use App\Models\KategoriProduk;
use App\Models\Packing as PackingModel;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Packing extends Component
{
    public $services = [];
    public $session_tgl_packing;
    public $session_kode_lot;
    public $total_berat = [];
    public $total_pcs = [];
    public $penerimaan_ikan;
    public $penerimaan_id;
    public $selectedTanggalPenerimaan;
    public $filteredPenerimaan = [];
    public $suppliers = [];
    public $selectedSupplier;
    public $kategori_byproduk_ct = [];
    public $kategori_produk = [];
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
    public $filter_tgl_packing_from;
    public $filter_tgl_packing_to;
    public $filter_tgl_penerimaan_from;
    public $filter_tgl_penerimaan_to;
    public $filter_jenis_penerimaan;

    // Insialisasi data
    public function mount()
    {
        // Inisialisasi variabel yang diperlukan
        $this->kategori_byproduk_ct = KategoriByprodukCt::all();
        $this->kategori_produk = KategoriProduk::all();
        $this->penerimaan_ikan = PenerimaanIkan::with('supplier')
            ->orderBy('tgl_penerimaan', 'desc')
            ->get();

        $this->selectedKategoriByproduk = [
            1 => [
                'type' => null,
                'value' => null,
            ],
            2 => [
                'type' => null,
                'value' => null,
            ],
            3 => [
                'type' => null,
                'value' => null,
            ],
            4 => [
                'type' => null,
                'value' => null,
            ],
            5 => [
                'type' => null,
                'value' => null,
            ],
            6 => [
                'type' => null,
                'value' => null,
            ],
            7 => [
                'type' => null,
                'value' => null,
            ]
        ];

        $this->rows = [];
        $this->filteredPenerimaan = collect();
        $this->addRow();

        // Inisialisasi session jika ada di URL
        if (request()->has('tgl_packing')) {
            $this->session_tgl_packing = request('tgl_packing');
        }
        if (request()->has('penerimaan_id')) {
            $this->penerimaan_id = request('penerimaan_id');
            $this->selectedTanggalPenerimaan = request('penerimaan_id');
        }

        // Load data jika semua filter terisi
        if ($this->session_tgl_packing && $this->penerimaan_id) {
            $this->loadData();
        } else {
            $this->reset(['rows']);
            $this->addRow();
        }
    }

    // Fungsi untuk memuat data yang sudah ada di database
    public function loadData($setHeader = true)
    {
        if ($setHeader) {
            $headerProduct = PackingModel::select('kategori_produk_id')
                ->where('tanggal', $this->session_tgl_packing)
                ->where('penerimaan_id', $this->penerimaan_id)
                ->where('kode_lot', $this->session_kode_lot)
                ->whereNotNull('kategori_produk_id')
                ->groupBy('kategori_produk_id')
                ->pluck('kategori_produk_id');
            $headerByProduct = PackingModel::select('kategori_byproduk_id')
                ->where('tanggal', $this->session_tgl_packing)
                ->where('penerimaan_id', $this->penerimaan_id)
                ->where('kode_lot', $this->session_kode_lot)
                ->whereNotNull('kategori_byproduk_id')
                ->groupBy('kategori_byproduk_id')
                ->pluck('kategori_byproduk_id');

            foreach ($headerProduct as $index => $kategoriId) {
                $this->selectedKategoriByproduk[$index + 1] = [
                    'type' => 'produk',
                    'value' => $kategoriId,
                ];
            }
            if (count($headerProduct) < 7) {
                foreach ($headerByProduct as $index => $kategoriId) {
                    $this->selectedKategoriByproduk[count($headerProduct) + $index + 1] = [
                        'type' => 'byproduk',
                        'value' => $kategoriId,
                    ];
                }
            }
        }

        $data = PackingModel::where('tanggal', $this->session_tgl_packing)
            ->where('penerimaan_id', $this->penerimaan_id)
            ->where('kode_lot', $this->session_kode_lot)
            ->where(function (Builder $q) {
                $byProductIds = array_map(
                    fn($el) => $el['value'],
                    array_filter(
                        $this->selectedKategoriByproduk,
                        function ($el) {
                            return $el['type'] == 'byproduk' && !is_null($el['value']);
                        }
                    ),
                );
                $productIds = array_map(
                    fn($el) => $el['value'],
                    array_filter(
                        $this->selectedKategoriByproduk,
                        function ($el) {
                            return $el['type'] == 'produk' && !is_null($el['value']);
                        }
                    ),
                );
                $q->whereIn('kategori_produk_id', $productIds)
                    ->orWhereIn('kategori_byproduk_id', $byProductIds);
            })
            ->get();

        if ($data->isEmpty()) {
            $this->rows = [];
            $this->addRow();
            $this->calculateTotals();
            return;
        }

        $groupedRows = [];

        foreach ($this->selectedKategoriByproduk as $key => $value) {
            $groupedRows[$key] = [
                'type' => $value['type'],
                'value' => $value['value'],
                'data' => empty($value['value'])
                    ? []
                    : $data->where(
                        $value['type'] == 'produk'
                            ? 'kategori_produk_id'
                            : 'kategori_byproduk_id',
                        $value['value']
                    )->values()->toArray(),
            ];
        }

        $this->rows = [];
        $maxRows = max(array_map(fn($el) => count($el['data']), $groupedRows));
        for ($i = 0; $i < $maxRows; $i++) {
            $newRow = [];
            for ($j = 1; $j <= 7; $j++) {
                if (isset($groupedRows[$j]['data'][$i])) {
                    $packing = $groupedRows[$j]['data'][$i];
                    $newRow['berat_produk' . $j] = $packing['berat_produk'];
                    $newRow['total_produk' . $j] = $packing['total_produk'];
                } else {
                    $newRow['berat_produk' . $j] = 0;
                    $newRow['total_produk' . $j] = 0;
                }
            }
            $this->rows[] = $newRow;
        }

        // remove emty rows
        $this->rows = array_values(array_filter($this->rows, function ($row) {
            for ($i = 1; $i <= 7; $i++) {
                if (!empty($row['berat_produk' . $i]) || !empty($row['total_produk' . $i])) {
                    return true;
                }
            }
            return false;
        }));

        $this->calculateTotals();
    }

    public function reloadRowsWithHeader(int $index, $value)
    {
        if (empty($value)) {
            $this->selectedKategoriByproduk[$index] = [
                'type' => null,
                'value' => null,
            ];
        } else {
            [$type, $value] = explode('_', $value);
            $this->selectedKategoriByproduk[$index] = [
                'type' => $type,
                'value' => $value,
            ];
        }

        $this->loadData(false);
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
        if (str_starts_with($propertyName, 'rows.')) {
            $this->calculateTotals();
        }
    }

    public function removeRow($index)
    {
        try {
            if (isset($this->rows[$index])) {
                unset($this->rows[$index]);
                $this->rows = array_values($this->rows); // Reindex array
                $this->calculateTotals();
                $this->saveAll(false);
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus data: ' . $e->getMessage());
            return;
        }
    }

    public function saveAll($validate = true)
    {
        try {
            if ($validate) {
                // Validasi input
                $this->validate([
                    'penerimaan_id' => 'required',
                    'session_tgl_packing' => 'required|date',
                ], [
                    'penerimaan_id.required' => 'Penerimaan harus dipilih',
                    'session_tgl_packing.required' => 'Tanggal service harus diisi',
                ]);

                // Validasi minimal satu data terisi
                $hasValidData = false;
                foreach ($this->rows as $row) {
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
            }

            DB::beginTransaction();

            // Hapus data lama berdasarkan filter yang sama
            PackingModel::where('tanggal', $this->session_tgl_packing)
                ->where('penerimaan_id', $this->penerimaan_id)
                ->where('kode_lot', $this->session_kode_lot)
                ->where(function (Builder $q) {
                    $byProductIds = array_map(
                        fn($el) => $el['value'],
                        array_filter(
                            $this->selectedKategoriByproduk,
                            function ($el) {
                                return $el['type'] == 'byproduk' && !is_null($el['value']);
                            }
                        ),
                    );
                    $productIds = array_map(
                        fn($el) => $el['value'],
                        array_filter(
                            $this->selectedKategoriByproduk,
                            function ($el) {
                                return $el['type'] == 'produk' && !is_null($el['value']);
                            }
                        ),
                    );
                    $q->whereIn('kategori_produk_id', $productIds)
                        ->orWhereIn('kategori_byproduk_id', $byProductIds);
                })
                ->delete();

            $savedCount = 0;

            // Simpan data baru
            foreach ($this->rows as $row) {
                // Proses setiap kolom produk (1-7)
                for ($i = 1; $i <= 7; $i++) {
                    $berat = (float)($row['berat_produk' . $i] ?? 0);
                    $total = (int)($row['total_produk' . $i] ?? 0);
                    $type = $this->selectedKategoriByproduk[$i]['type'] ?? null;
                    $kategoriId = $this->selectedKategoriByproduk[$i]['value'] ?? null;

                    // Hanya simpan jika ada kategori, dan minimal satu nilai diisi
                    if (!empty($kategoriId) && !empty($type) && ($berat > 0 || $total > 0)) {
                        try {
                            PackingModel::create([
                                'kode_lot' => $this->session_kode_lot,
                                'tanggal' => $this->session_tgl_packing,
                                'penerimaan_id' => $this->penerimaan_id,
                                'kategori_byproduk_id' => $type === 'byproduk' ? $kategoriId : null,
                                'kategori_produk_id' => $type === 'produk' ? $kategoriId : null,
                                'berat_produk' => $berat,
                                'total_produk' => $total,
                            ]);
                            $savedCount++;
                        } catch (\Exception $e) {
                            Log::error('Gagal menyimpan data: ' . $e->getMessage() . ' - Data: ' . json_encode([
                                'tanggal' => $this->session_tgl_packing,
                                'penerimaan_id' => $this->penerimaan_id,
                                'kategori_byproduk_id' => $type === 'byproduk' ? $kategoriId : null,
                                'kategori_produk_id' => $type === 'produk' ? $kategoriId : null,
                                'berat_produk' => $berat,
                                'total_produk' => $total,
                            ]));
                            throw $e;
                        }
                    }
                }
            }

            if ($validate && $savedCount === 0) {
                throw new \Exception(
                    'Tidak ada data yang berhasil disimpan. Pastikan Anda telah mengisi data dengan benar.'
                );
            }

            DB::commit();

            session()->flash('message', 'Data berhasil disimpan');
            $this->loadData();
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
        $this->selectedKategoriByproduk = [
            1 => [
                'type' => null,
                'value' => null,
            ],
            2 => [
                'type' => null,
                'value' => null,
            ],
            3 => [
                'type' => null,
                'value' => null,
            ],
            4 => [
                'type' => null,
                'value' => null,
            ],
            5 => [
                'type' => null,
                'value' => null,
            ],
            6 => [
                'type' => null,
                'value' => null,
            ],
            7 => [
                'type' => null,
                'value' => null,
            ]
        ];
    }

    public function delete($id)
    {
        PackingModel::destroy($id);
        $this->filterData();
    }

    // Method untuk filter data
    public function filterData()
    {
        $query = PackingModel::with(['penerimaan.supplier', 'kategoriByproduk']);

        // Filter berdasarkan tanggal service
        if ($this->session_tgl_packing) {
            $query->whereDate('tanggal', $this->session_tgl_packing);
        }

        // Filter berdasarkan tanggal penerimaan
        if ($this->selectedTanggalPenerimaan) {
            $penerimaanIds = PenerimaanIkan::where(
                'penerimaan_id',
                $this->selectedTanggalPenerimaan
            )
                ->pluck('penerimaan_id');
            $query->whereIn('penerimaan_id', $penerimaanIds);
        }

        // Filter berdasarkan jenis penerimaan
        if ($this->penerimaan_id) {
            $query->where('penerimaan_id', $this->penerimaan_id);
        }

        $this->services = $query->orderBy('created_at', 'desc')->get();
    }

    // Method untuk mendapatkan nama produk berdasarkan ID
    protected function getProductName($kategoriId)
    {
        if (!$kategoriId) return 'Produk Tidak Diketahui';

        $product = KategoriByprodukCt::find($kategoriId);
        return $product ? $product->nama_produk : 'Produk Tidak Diketahui';
    }

    public function calculateTotalCutting(int $index)
    {
        if (empty($this->rows[$index]) || empty($this->rows[$index]['no_batch']) || empty($this->penerimaan_id)) {
            return;
        }

        $row = [
            'no_batch' => $this->rows[$index]['no_batch'],
        ];

        for ($i = 1; $i <= 7; $i++) {
            $kategoriId = $this->selectedKategoriByproduk[$i] ?? null;

            if (empty($kategoriId)) {
                continue;
            }

            $cuttings = Cutting::where('penerimaan_id', $this->penerimaan_id)
                ->where('kategori_byproduk_id', $kategoriId)
                ->where('no_batch', $this->rows[$index]['no_batch'])
                ->select('total_produk', 'berat_produk')
                ->get();

            $weightSum = 0;
            $pcsSum = 0;

            $cuttings->each(function ($cutting) use (&$weightSum, &$pcsSum) {
                $weight = is_array($cutting->berat_produk) ? (float)$cutting->berat_produk[0] : (float)$cutting->berat_produk;
                $total = is_array($cutting->total_produk) ? (int)$cutting->total_produk[0] : (int)$cutting->total_produk;

                $weightSum += $weight;
                $pcsSum += $total;
            });

            $row['berat_produk' . $i] = $weightSum;
            $row['total_produk' . $i] = $pcsSum;
        }

        // Update baris dengan data yang dihitung
        $this->rows[$index] = array_merge($this->rows[$index], $row);
    }

    public function updatedSessionKodeLot($value)
    {
        if ($this->session_tgl_packing && $this->penerimaan_id && $value) {
            $this->loadData();
        } else {
            $this->resetForm();
        }
    }

    // Update method updatedSessionTglPacking untuk reset data jika tanggal berubah
    public function updatedSessionTglPacking($value)
    {
        $this->reset([
            'selectedTanggalPenerimaan',
            'penerimaan_id',
            'rows'
        ]);
        $this->addRow();
    }

    public function render()
    {
        $this->filterData();

        return view('livewire.packing', [
            'session_tgl_packing' => $this->session_tgl_packing,
            'selectedSupplier' => $this->selectedSupplier,
            'penerimaan_ikan' => $this->penerimaan_ikan,
        ]);
    }
}
