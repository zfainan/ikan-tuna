<?php

namespace App\Livewire;

use App\Models\CuttingL;
use App\Models\PenerimaanIkan;
use App\Models\GradeL;
use App\Models\GradeService;
use App\Models\GradeHservice;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Renderless;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CuttingByL extends Component
{         
    // Data Cutting loin               
    public $session_tggl_cutting;                  
    public $session_tggl_injek_co;                  
    public $session_tggl_service; 
    public $no_batch;
    public $suhu_loin;
    public $no_loin;

    public $berat_loin = 0;                         // penjumlahan berat
    public $total_loin = 0;
    public $berat_rm = [1=> 0, 2=> 0, 3=> 0];       // array rm
    public $total_rm = 0;
    public $berat_hs = [1=> 0, 2=> 0, 3=> 0];       // array hs
    public $total_hs = 0;
    public $pcs_rm = [1=> 0, 2=> 0, 3=> 0];
    public $pcs_hs = [1=> 0, 2=> 0, 3=> 0];
    
    public $penerimaan_id;                          // Data Penerimaan
    public $no_ikan;
    public $selectedPenerimaan;
    public $penerimaan_ikan;
    public $filteredPenerimaan = [];
    public $selectedTanggalPenerimaan;              // selected tgl penerimaan
    public $updateFilterTanggal;
    public $sizingLoin = [];
    public $gradingService = [];
    public $gradingHservice = [];

    //newpropertiform
    public $cutting_id;
    public $isModalOpen = false;
    public $search = '';
    public $perPage = 10;
    public $sortField = 'tggl_cutting';
    public $sortDirection = 'desc';
    public $allBatches = [];

    public $rows = [];

// inisialisasi data
    public function mount()
    {
        $this->penerimaan_ikan = PenerimaanIkan::with(['supplier' => function ($query) {
            $query->select('supplier_id', 'nama_supplier', 'alamat');
        }])
            ->select('penerimaan_ikans.*')
            ->orderBy('tgl_penerimaan', 'desc')
            ->get()
            ->unique('supplier_id')
            ->map(function ($item) {
                $item->tgl_penerimaan = \Carbon\Carbon::parse($item->tgl_penerimaan)->toDateString();
                return $item;
            })
            ->filter(function ($item) {
                return $item->supplier_id !== null;
            })
            ->values();

        $this->sizingLoin = GradeL::all();
        $this->selectedSizingLoin = [1 => null];
        $this->gradingService = \App\Models\GradeService::all();
        $this->selectedGradingService = [1 => null, 2 => null, 3 => null];
        $this->gradingHservice = \App\Models\GradeHservice::all();
        $this->selectedGradingHservice = [1 => null, 2 => null, 3 => null];

        //Inisialisasi filter tanggal
        $this->filterTanggalCutting = $this->session_tggl_cutting;
        $this->filterTanggalInjekCo = $this->session_tggl_injek_co;
        $this->filterTanggalService = $this->session_tggl_service;
        if ($this->filterTanggalCutting || $this->filterTanggalInjekCo || $this->filterTanggalService) {
            $this->searchByBatch();
        }

        //inisialisasi array
        $this->berat_rm = [0, 0, 0];
        $this->pcs_rm = [0, 0, 0];
        $this->berat_hs = [0, 0, 0];
        $this->pcs_hs = [0, 0, 0];
        $this->pcs_loin = 0;
        $this->berat_loin = 0;
        $this->selectedTanggalPenerimaan = null;
        $this->filteredPenerimaan = collect();
        
        //inisialisasi no batch & load data
        $this->allBatches = $this->getAvailableBatches();
        $this->loadData();
    }

//otomatis dipanggil jika penerimaan_id berubah
    public function updatedPenerimaanId($value) 
    {
        if ($value) {
            $penerimaan = PenerimaanIkan::find($value);
            if ($penerimaan) {
                $this->selectedPenerimaan = $penerimaan;
                $this->no_ikan = $penerimaan->no_ikan;
            }
        }else {
            $this->selectedPenerimaan = null;
            $this->no_ikan = null;
        }
    }

//memanggil tanggal penerimaan ke cutting/service loin
    public function updatedSelectedTanggalPenerimaan($value)
    {
        $this->selectedTanggalPenerimaan = $value;
        $this->penerimaan_id = $value;
        $this->no_ikan = null;

        if ($value) {
            $this->filteredPenerimaan = $this->penerimaan_ikan->filter(function ($item) use ($value) {
                return \Carbon\Carbon::parse($item->tgl_penerimaan)->toDateString() === $value;
            })->values();
        } else {
            $this->filteredPenerimaan = collect();
        }
    }
    public function updatedSelectedGradingService($value, $key)
    {
        $this->updatedSelectedGradingService[$key] = $value;
    }
    public function updatedSelectedGradingHservice($value, $key)
    {
        $this->updatedSelectedGradingHservice[$key] = $value;
    }

//tambah row
    public function addRow()
    {
        $this->rows[] = [
            'berat_loin' => '',
            'suhu_loin' => '',
            'no_loin' => '',
            'grade_size_id' => null,
            'grade_service_id' => null,
            'grade_servicehs_id' => null,
            'penerimaan_id' => null,
            'berat_1' => '', 'berat_2' => '', 'berat_3' => '',
            'berat_4' => '', 'berat_5' => '', 'berat_6' => '',
        ];
    }

//hapus row
    public function removeRow($index)
    {
        try {
            $row = $this->rows[$index] ?? null;
            if (isset($row['cutting_id'])) {
                \App\Models\CuttingL::where('cutting_id', $row['cutting_id'])->delete();
            }
            unset($this->rows[$index]);
            $this->rows = array_values($this->rows);
            $this->calculateTotals();

            if ($this->no_batch) {
                $this->searchByBatch();
            }
        } catch (\Exception $e) {

        }
    }

//hitung total berat & pcs
    public function calculateTotals()
    {
        $this->berat_loin = 0;
        $this->berat_rm = [0, 0, 0];
        $this->pcs_rm = [0, 0, 0];
        $this->berat_hs = [0, 0, 0];
        $this->pcs_hs = [0, 0, 0];

        //inisialisasi array untuk berat
        $berat = [];
        $berat_rm = 0;
        $berat_hs = 0;
        for ($i=0; $i <= 5; $i++) {
            $berat[$i] = 0;
        }

        //hitung total & pcs dari semua rows
        foreach($this->rows as $row) {
            //total berat (cutting loin)
            $this->berat_loin += (float) ($row['berat_loin'] ?? 0);

            //total berat (RM service & pcs)
            for ($i= 1; $i <= 3; $i++) {
                $berat = (float) ($row['berat_' . $i] ?? 0);
                $this->berat_rm[$i-1] += $berat;
                if ($berat > 0) {
                    $this->pcs_rm[$i-1]++;
                }
            } 
            
            //total berat (HS service & pcs)
            for ($i=4; $i <= 6; $i++) {
                $berat = (float) ($row['berat_' . $i] ?? 0);
                $this->berat_hs[$i-4] += $berat;
                if ($berat > 0) {
                    $this->pcs_hs[$i-4]++;
                }
            }
        }
        // property untuk digunakan pada view
        $this->berat = $berat;
        $this->berat_rm = array_map('floatval', $this->berat_rm);
        $this->berat_hs = array_map('floatval', $this->berat_hs);
        $this->pcs_loin = count($this->rows);
    }

//untuk mendapatkan daftar batch
private function getAvailableBatches()
{
    return CuttingL::select('no_batch')
        ->distinct()
        ->orderBy('no_batch', 'desc')
        ->pluck('no_batch')
        ->toArray();
}

//reset form
private function resetForm()
{
    $this->reset([
        'cutting_id', 'no_loin', 'berat_loin', 'suhu_loin', 
        'pcs_loin', 'berat_rm', 'pcs_rm', 'berat_hs', 'pcs_hs',
        'penerimaan_id', 'grade_size_id', 'grade_service_id', 'grade_servicehs_id'
    ]);
}

//modal form
    public function create()
    {
        $this->resetForm();
        $this->isModalOpen = true;
    }

//edit data
    public function edit($id)
    {
        $this->cutting_id = $id;
        $cutting = CuttingL::find($id);
        $this->no_loin = $cutting->no_loin;
        $this->berat_loin = $cutting->berat_loin;
        $this->suhu_loin = $cutting->suhu_loin;
        $this->pcs_loin = $cutting->pcs_loin;
        $this->berat_rm = $cutting->berat_rm;
        $this->pcs_rm = $cutting->pcs_rm;
        $this->berat_hs = $cutting->berat_hs;
        $this->pcs_hs = $cutting->pcs_hs;
        $this->penerimaan_id = $cutting->penerimaan_id;
        $this->grade_size_id = $cutting->grade_size_id;
        $this->grade_service_id = $cutting->grade_service_id;
        $this->grade_servicehs_id = $cutting->grade_servicehs_id;

        $this->isModalOpen = true;
    }

//simpan data
    public function saveAll()
    {
        $validatedData = $this->validate([
            'no_loin' => 'required|string|max:50',
            'berat_loin' => 'required|numeric|min:0',
            'suhu_loin' => 'required|numeric',
            'no_batch' => 'required|string|max:50',
            'session_tggl_cutting' => 'required|date',
            'session_tggl_injek_co' => 'required|date',
            'session_tggl_service' => 'required|date',
        ]);

        try {
            DB::beginTransaction();

            $data = [
                'tggl_cutting' => $this->session_tggl_cutting,
                'tggl_injek_co' => $this->session_tggl_injek_co,
                'tggl_service' => $this->session_tggl_service,
                'no_batch' => $this->no_batch,
                'no_loin' => $this->no_loin,
                'berat_loin' => $this->berat_loin,
                'suhu_loin' => $this->suhu_loin,
                'pcs_loin' => $this->pcs_loin ?? 0,
                'berat_rm' => $this->berat_rm ?? 0,
                'pcs_rm' => $this->pcs_rm ?? 0,
                'berat_hs' => $this->berat_hs ?? 0,
                'pcs_hs' => $this->pcs_hs ?? 0,
                'penerimaan_id' => $this->penerimaan_id,
                'grade_size_id' => $this->grade_size_id,
                'grade_service_id' => $this->grade_service_id,
                'grade_servicehs_id' => $this->grade_servicehs_id,
            ];

            if ($this->cutting_id) {
                // Update existing
                CuttingL::find($this->cutting_id)->update($data);
                session()->flash('message', 'Data berhasil diupdate.');
            } else {
                // Create new
                CuttingL::create($data);
                session()->flash('message', 'Data berhasil ditambahkan.');
            }

            DB::commit();
            $this->isModalOpen = false;
            $this->loadData();
            
        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

//hapus data
    public function delete($id)
    {
        try {
            $cutting = CuttingL::findOrFail($id);
            $cutting->delete();
            session()->flash('message', 'Data berhasil dihapus.');
            $this->loadData();
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

//sorting
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
        $this->loadData();
    }
    public function searchByBatch()
    {
        return CuttingL::with(['penerimaan', 'grade_size', 'grade_service', 'grade_servicehs'])
            ->where('no_batch', $this->no_batch)
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

//loadData dengan filter
    public function loadData()
    {
        $query = CuttingL::query()
            ->with([
                'penerimaan',
                'grade_size',
                'grade_service',
                'grade_servicehs',
            ])
            ->when($this->search, function($query) {
                $query->where('no_loin', 'like', '%' . $this->search . '%')
                    ->orWhere('no_batch', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortField, $this->sortDirection);

        $this->cuttingData = $query->paginate($this->perPage);
    }

//render view
    public function render()
    {
        // filter berdasarlan tgl penerimaan
        $filteredPenerimaan = $this->penerimaan_ikan;
        if ($this->selectedTanggalPenerimaan) {
            //jika memilih tgl penerimaan
            $filteredPenerimaan = $this->penerimaan_ikan->filter(function($item) {
                return $item->tgl_penerimaan == $this->selectedTanggalPenerimaan;
            });
        }
        //perhitungan total & pcs
        $this->calculateTotals();
        $this->allBatches = $this->getAvailableBatches();

        $this->loadData();
        $query = CuttingL::query()
            ->with(['penerimaan', 'grade_size', 'grade_service', 'grade_servicehs'])
            ->when($this->search, function($query) {
                $query->where('no_loin', 'like', '%' . $this->search . '%')
                    ->orWhere('no_batch', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortField, $this->sortDirection);

        $cuttingData = collect();
        if (!empty($this->no_batch)) {
            $cuttingData = $this->searchByBatch();
        } else {
            $cuttingData = $query->paginate($this->perPage);
        }

        $selectedGradingService = $this->selectedGradingService ?? [1 => null, 2 => null, 3 => null];
        $gradingService = $this->gradingService ?? collect();
        $selectedGradingHservice = $this->selectedGradingHservice ?? [4 => null, 5 => null, 6 => null];
        $gradingHservice = $this->gradingHservice ?? collect();

        return view('livewire.cuttingl', [
            'cuttingData' => $cuttingData,
            'penerimaan_ikan' => $this->penerimaan_ikan,
            'sizingLoin' => $this->sizingLoin,
            'gradingService' => $this->gradingService,
            'gradingHservice' => $this->gradingHservice,
            ]);
    }
}