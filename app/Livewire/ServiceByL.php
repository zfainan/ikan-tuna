<?php

namespace App\Livewire;

use App\Models\PenerimaanIkan;
use App\Models\KategoriProduk;
use App\Models\CuttingL;
use App\Models\ServiceL;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ServiceByL extends Component
{
    //Properti untuk form input dan filter
    public $servicel;
    public $session_tgl_service;
    public $selectedTanggalPenerimaan;
    public $selectedCuttingByl;
    public $filteredPenerimaan;
    public $cutting_ikan;
    public $penerimaan_ikan;
    public $penerimaan_id;
    public $kategori_produk;
    public $total_berat = [];
    public $total_pcs = [];
    public $rows = [];
    public $selectedKategori1 = null;
    public $selectedKategori2 = null;
    public $selectedKategori3 = null;
    public $selectedKategori4 = null;
    public $selectedKategori5 = null;
    public $selectedKategori6 = null;
    public $selectedKategori7 = null;

    public function mount()
    {
        $this->filteredPenerimaan = PenerimaanIkan::all();
        $this->session_tgl_service = Carbon::now()->format('Y-m-d');
        $this->loadCuttingData();
        $this->addRow();
    }

    public function addRow()
    {
        $newRow = [];
        for ($i = 1; $i <= 7; $i++) {
            $newRow['berat_produk' . $i] = 0;
            $newRow['total_produk' . $i] = 0;
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

    public function removeRow($index)
    {
        unset($this->rows[$index]);
        $this->rows = array_values($this->rows);
        $this->calculateTotals();
    }

    public function loadCuttingData()
    {
        $query = CuttingL::query()
            ->with([
                'penerimaan.supplier',
            ])
            ->select('cuttingls.*')
            ->join('penerimaan_ikans', 'cuttingls.penerimaan_id', '=', 'penerimaan_ikans.penerimaan_id')
            ->leftJoin('suppliers', 'penerimaan_ikans.supplier_id', '=', 'suppliers.supplier_id');

        if ($this->selectedTanggalPenerimaan) {
            $query->where('penerimaan_ikans.penerimaan_id', $this->selectedTanggalPenerimaan);
        }

        $this->cutting_ikan = $query->get();
        $this->penerimaan_ikan = PenerimaanIkan::whereHas('cuttingls')->get();
        $this->kategori_produk = KategoriProduk::all();
    }

    public function loadData()
    {
        $data = ServiceL::where('tgl_service', $this->session_tgl_service)
            ->where('penerimaan_id', $this->penerimaan_id)
            ->where('cuttingl_id', $this->selectedCuttingByl)
            ->get();

        if ($data->isEmpty()) {
            $this->rows = [];
            $this->addRow();
            $this->selectedKategori1 = null;
            $this->selectedKategori2 = null;
            $this->selectedKategori3 = null;
            $this->selectedKategori4 = null;
            $this->selectedKategori5 = null;
            $this->selectedKategori6 = null;
            $this->selectedKategori7 = null;
        } else {
            $this->rows = $data->map(fn($c) => [
                'berat_produk1' => $c->berat_1,
                'total_produk1' => $c->pcs_1,
                'berat_produk2' => $c->berat_2,
                'total_produk2' => $c->pcs_2,
                'berat_produk3' => $c->berat_3,
                'total_produk3' => $c->pcs_3,
                'berat_produk4' => $c->berat_4,
                'total_produk4' => $c->pcs_4,
                'berat_produk5' => $c->berat_5,
                'total_produk5' => $c->pcs_5,
                'berat_produk6' => $c->berat_6,
                'total_produk6' => $c->pcs_6,
                'berat_produk7' => $c->berat_7,
                'total_produk7' => $c->pcs_7,
            ])->toArray();
            $first = $data->first();
            $this->selectedKategori1 = $first->kategori_1;
            $this->selectedKategori2 = $first->kategori_2;
            $this->selectedKategori3 = $first->kategori_3;
            $this->selectedKategori4 = $first->kategori_4;
            $this->selectedKategori5 = $first->kategori_5;
            $this->selectedKategori6 = $first->kategori_6;
            $this->selectedKategori7 = $first->kategori_7;
        }

        $this->calculateTotals();
    }

    public function updateSelectedCuttingByl($value)
    {
        if ($value) {
            $cutting = CuttingL::with('penerimaan.supplier')
                ->find($value);

            if ($cutting && $cutting->penerimaan) {
                $this->filteredPenerimaan = collect([$cutting->penerimaan]);
                $this->penerimaan_id = $cutting->penerimaan_id;
            } else {
                $this->filteredPenerimaan = collect();
                $this->penerimaan_id = null;
            }
        } else {
            $this->filteredPenerimaan = collect();
            $this->penerimaan_id = null;
        }
    }

    public function updateSelectedTanggalPenerimaan($value)
    {
        $this->selectedTanggalPenerimaan = $value;
        $this->loadCuttingData();
    }

    public function updatedPenerimaanId($value)
    {
        $this->loadData();
    }

    public function saveAll()
    {
        DB::beginTransaction();

        try {
            ServiceL::where('tgl_service', $this->session_tgl_service)
                ->where('penerimaan_id', $this->penerimaan_id)
                ->where('cuttingl_id', $this->selectedCuttingByl)
                ->delete();

            foreach ($this->rows as $row) {
                ServiceL::create([
                    'tgl_service' => $this->session_tgl_service,
                    'cuttingl_id' => $this->selectedCuttingByl,
                    'penerimaan_id' => $this->penerimaan_id,

                    // Col 1-7
                    'kategori_1' => $this->selectedKategori1,
                    'berat_1' => $row['berat_produk1'] ?? 0,
                    'pcs_1' => $row['total_produk1'] ?? 0,
                    'kategori_2' => $this->selectedKategori2,
                    'berat_2' => $row['berat_produk2'] ?? 0,
                    'pcs_2' => $row['total_produk2'] ?? 0,
                    'kategori_3' => $this->selectedKategori3,
                    'berat_3' => $row['berat_produk3'] ?? 0,
                    'pcs_3' => $row['total_produk3'] ?? 0,
                    'kategori_4' => $this->selectedKategori4,
                    'berat_4' => $row['berat_produk4'] ?? 0,
                    'pcs_4' => $row['total_produk4'] ?? 0,
                    'kategori_5' => $this->selectedKategori5,
                    'berat_5' => $row['berat_produk5'] ?? 0,
                    'pcs_5' => $row['total_produk5'] ?? 0,
                    'kategori_6' => $this->selectedKategori6,
                    'berat_6' => $row['berat_produk6'] ?? 0,
                    'pcs_6' => $row['total_produk6'] ?? 0,
                    'kategori_7' => $this->selectedKategori7,
                    'berat_7' => $row['berat_produk7'] ?? 0,
                    'pcs_7' => $row['total_produk7'] ?? 0,
                ]);
            }

            DB::commit();
            session()->flash('message', 'Data berhasil disimpan');
            $this->loadData();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            session()->flash('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.servicel', [
            'filteredPenerimaan' => $this->filteredPenerimaan,
            'cutting_ikan' => $this->cutting_ikan,
        ]);
    }
}
