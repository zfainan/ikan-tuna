<?php

namespace App\Livewire;

use App\Models\ServiceL;
use App\Models\PenerimaanIkan;
use App\Models\Supplier;
use App\Models\KategoriProduk;
use App\Models\CuttingL;
use Livewire\Component;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;


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
    public $kategori_produk;
    public $total_berat = [];
    public $total_pcs = [];
    public $rows = [];

// inisialisasi data
    public function mount()
    {
        $this->filteredPenerimaan = PenerimaanIkan::all();
        $this->session_tgl_service = Carbon::now()->format('Y-m-d');
        $this->loadCuttingData();
        $this->addRow();
    }

//add, update, remove row
    public function addRow ()
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
        for($i = 1; $i <= 7; $i++) {
            $this->total_berat[$i] = 0;
            $this->total_pcs[$i] = 0;
        }
        foreach ($this->rows as $row) {
            for($i = 1; $i <= 7; $i++) {
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
                'penerimaan' => function($q) {
                    $q->with('supplier');
                }
            ])
            ->select('cuttingls.*')
            ->join('penerimaan_ikans', 'cuttingls.penerimaan_id', '=', 'penerimaan_ikans.penerimaan_id')
            ->leftJoin('suppliers', 'penerimaan_ikans.supplier_id', '=', 'suppliers.supplier_id');
            
        if ($this->selectedTanggalPenerimaan) {
            $query->where('penerimaan_ikans.penerimaan_id', $this->selectedTanggalPenerimaan);
        }
        
        $this->cutting_ikan = $query->get();
        $this->penerimaan_ikan = $query->get();
        $this->kategori_produk = KategoriProduk::all();
    }

    public function updateSelectedCuttingByl($value)
    {
        if($value) {
            $cutting = CuttingL::with('penerimaan.supplier')
            ->find($value);

            if($cutting && $cutting->penerimaan) {
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

    public function render()
    {
        return view('livewire.servicel', [
            'filteredPenerimaan' => $this->filteredPenerimaan,
            'cutting_ikan' => $this->cutting_ikan,
        ]);
    }
}

