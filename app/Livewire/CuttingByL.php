<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\CuttingL;
use App\Models\PenerimaanIkan;
use App\Models\GradeL;
use App\Models\GradeService;
use App\Models\GradeHService;
use Barryvdh\DomPDF\Facade\Pdf;

class CuttingByL extends Component
{
    /* =======================
     * SESSION / FILTER INPUT
     * ======================= */
    public $session_tggl_cutting;
    public $session_tggl_injek_co;
    public $session_tggl_service;

    public $penerimaan_ikan;
    public $filteredPenerimaan = [];
    public $selectedTanggalPenerimaan;
    public $penerimaan_id;

    /* =======================
     * MASTER DATA
     * ======================= */
    public $sizingLoin;
    public $gradingService;
    public $gradingHservice;

    /* =======================
     * SPREADSHEET DATA
     * ======================= */
    public $rows = [];
    public $noBatch = '';
    public ?int $selectedSizingLoin = null;
    public ?int $selectedGradingService1 = null;
    public ?int $selectedGradingService2 = null;
    public ?int $selectedGradingService3 = null;
    public ?int $selectedGradingHService1 = null;
    public ?int $selectedGradingHService2 = null;
    public ?int $selectedGradingHService3 = null;

    /* =======================
     * TOTALS
     * ======================= */
    public $berat_loin = 0;
    public $pcs_loin = 0;

    public $berat_rm = [0, 0, 0];
    public $pcs_rm = [0, 0, 0];

    public $berat_hs = [0, 0, 0];
    public $pcs_hs = [0, 0, 0];

    /* =======================
     * INIT
     * ======================= */
    public function mount()
    {
        $this->penerimaan_ikan = PenerimaanIkan::with('supplier')
            ->orderBy('tgl_penerimaan', 'desc')
            ->get();

        $this->sizingLoin = GradeL::all();
        $this->gradingService = GradeService::all();
        $this->gradingHservice = GradeHService::all();

        $this->rows = [];
        $this->addRow();
    }

    /* =======================
     * FILTER HANDLER
     * ======================= */
    public function updatedSessionTgglCutting()
    {
        $this->reset(['session_tggl_injek_co', 'session_tggl_service', 'selectedTanggalPenerimaan', 'penerimaan_id', 'rows']);
        $this->addRow();
    }

    public function updatedSessionTgglInjekCo()
    {
        $this->reset(['session_tggl_service', 'selectedTanggalPenerimaan', 'penerimaan_id', 'rows']);
        $this->addRow();
    }

    public function updatedSelectedTanggalPenerimaan($value)
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

    public function updatedPenerimaanId()
    {
        if ($this->session_tggl_cutting && $this->session_tggl_injek_co && $this->session_tggl_service && $this->penerimaan_id) {
            $first = CuttingL::where('tggl_cutting', $this->session_tggl_cutting)
                ->where('tggl_injek_co', $this->session_tggl_injek_co)
                ->where('tggl_service', $this->session_tggl_service)
                ->where('penerimaan_id', $this->penerimaan_id)
                ->first();

            if ($first) {
                $this->noBatch = $first->no_batch;
                $this->selectedSizingLoin = $first->grade_size_id;
                $this->selectedGradingService1 = $first->rm_grade_1;
                $this->selectedGradingService2 = $first->rm_grade_2;
                $this->selectedGradingService3 = $first->rm_grade_3;
                $this->selectedGradingHService1 = $first->hs_grade_1;
                $this->selectedGradingHService2 = $first->hs_grade_2;
                $this->selectedGradingHService3 = $first->hs_grade_3;
            } else {
                $this->resetHeader();
            }

            $this->loadData();
        } else {
            $this->resetHeader();
            $this->reset(['rows']);
            $this->addRow();
        }
    }

    /* =======================
     * ROW MANIPULATION
     * ======================= */
    public function addRow()
    {
        $this->rows[] = [
            'cuttingl_id' => null,
            'no_batch' => '',
            'grade_size_id' => null,
            'no_loin' => '',
            'berat_loin' => null,
            'suhu_loin' => null,

            // RM
            'rm_grade_1' => null,
            'rm_grade_2' => null,
            'rm_grade_3' => null,
            'rm_berat_1' => null,
            'rm_berat_2' => null,
            'rm_berat_3' => null,

            // HS
            'hs_grade_1' => null,
            'hs_grade_2' => null,
            'hs_grade_3' => null,
            'hs_berat_1' => null,
            'hs_berat_2' => null,
            'hs_berat_3' => null,
        ];
    }

    public function removeRow($index)
    {
        if (!isset($this->rows[$index])) return;

        $id = $this->rows[$index]['cuttingl_id'] ?? null;
        if ($id) {
            CuttingL::where('cuttingl_id', $id)->delete();
        }

        unset($this->rows[$index]);
        $this->rows = array_values($this->rows);

        if (empty($this->rows)) {
            $this->addRow();
        }

        $this->calculateTotals();
    }

    public function resetHeader(): void
    {
        $this->reset([
            'noBatch',
            'selectedSizingLoin',
            'selectedGradingService1',
            'selectedGradingService2',
            'selectedGradingService3',
            'selectedGradingHService1',
            'selectedGradingHService2',
            'selectedGradingHService3'
        ]);
    }

    /* =======================
     * LOAD DATA (SPREADSHEET)
     * ======================= */
    public function loadData()
    {
        $first = CuttingL::where('tggl_cutting', $this->session_tggl_cutting)
            ->where('tggl_injek_co', $this->session_tggl_injek_co)
            ->where('tggl_service', $this->session_tggl_service)
            ->where('penerimaan_id', $this->penerimaan_id)
            ->where('no_batch', $this->noBatch)
            ->first();

        $query = CuttingL::where('tggl_cutting', $this->session_tggl_cutting)
            ->where('tggl_injek_co', $this->session_tggl_injek_co)
            ->where('tggl_service', $this->session_tggl_service)
            ->where('penerimaan_id', $this->penerimaan_id)
            ->where('no_batch', $this->noBatch);

        if ($first) {
            $query->where('grade_size_id', $first->grade_size_id);
            $this->selectedGradingService1 = $first->rm_grade_1;
            $this->selectedGradingService2 = $first->rm_grade_2;
            $this->selectedGradingService3 = $first->rm_grade_3;
            $this->selectedGradingHService1 = $first->hs_grade_1;
            $this->selectedGradingHService2 = $first->hs_grade_2;
            $this->selectedGradingHService3 = $first->hs_grade_3;
        } else {
            $this->selectedGradingService1 = null;
            $this->selectedGradingService2 = null;
            $this->selectedGradingService3 = null;
            $this->selectedGradingHService1 = null;
            $this->selectedGradingHService2 = null;
            $this->selectedGradingHService3 = null;
        }

        $data = $query->get();

        $this->rows = $data->map(fn($c) => [
            'cuttingl_id' => $c->cuttingl_id,
            'no_batch' => $c->no_batch,
            'grade_size_id' => $c->grade_size_id,
            'no_loin' => $c->no_loin,
            'berat_loin' => $c->berat_loin,
            'suhu_loin' => $c->suhu_loin,

            'rm_grade_1' => $c->rm_grade_1,
            'rm_grade_2' => $c->rm_grade_2,
            'rm_grade_3' => $c->rm_grade_3,
            'rm_berat_1' => $c->rm_berat_1,
            'rm_berat_2' => $c->rm_berat_2,
            'rm_berat_3' => $c->rm_berat_3,

            'hs_grade_1' => $c->hs_grade_1,
            'hs_grade_2' => $c->hs_grade_2,
            'hs_grade_3' => $c->hs_grade_3,
            'hs_berat_1' => $c->hs_berat_1,
            'hs_berat_2' => $c->hs_berat_2,
            'hs_berat_3' => $c->hs_berat_3,
        ])->toArray();

        if (empty($this->rows)) {
            $this->addRow();
        }

        $this->calculateTotals();
    }

    public function loadDataByHeader()
    {
        $data = CuttingL::where('tggl_cutting', $this->session_tggl_cutting)
            ->where('tggl_injek_co', $this->session_tggl_injek_co)
            ->where('tggl_service', $this->session_tggl_service)
            ->where('penerimaan_id', $this->penerimaan_id)
            ->where('no_batch', $this->noBatch)
            ->where('grade_size_id', $this->selectedSizingLoin)
            ->get();

        $this->rows = $data->map(fn($c) => [
            'cuttingl_id' => $c->cuttingl_id,
            'no_batch' => $c->no_batch,
            'grade_size_id' => $c->grade_size_id,
            'no_loin' => $c->no_loin,
            'berat_loin' => $c->berat_loin,
            'suhu_loin' => $c->suhu_loin,

            'rm_grade_1' => $c->rm_grade_1,
            'rm_grade_2' => $c->rm_grade_2,
            'rm_grade_3' => $c->rm_grade_3,
            'rm_berat_1' => $c->rm_berat_1,
            'rm_berat_2' => $c->rm_berat_2,
            'rm_berat_3' => $c->rm_berat_3,

            'hs_grade_1' => $c->hs_grade_1,
            'hs_grade_2' => $c->hs_grade_2,
            'hs_grade_3' => $c->hs_grade_3,
            'hs_berat_1' => $c->hs_berat_1,
            'hs_berat_2' => $c->hs_berat_2,
            'hs_berat_3' => $c->hs_berat_3,
        ])->toArray();

        $first = $data->first();
        if ($first) {
            $this->selectedGradingService1 = $first->rm_grade_1;
            $this->selectedGradingService2 = $first->rm_grade_2;
            $this->selectedGradingService3 = $first->rm_grade_3;
            $this->selectedGradingHService1 = $first->hs_grade_1;
            $this->selectedGradingHService2 = $first->hs_grade_2;
            $this->selectedGradingHService3 = $first->hs_grade_3;
        } else {
            $this->selectedGradingService1 = null;
            $this->selectedGradingService2 = null;
            $this->selectedGradingService3 = null;
            $this->selectedGradingHService1 = null;
            $this->selectedGradingHService2 = null;
            $this->selectedGradingHService3 = null;
        }

        if (empty($this->rows)) {
            $this->addRow();
        }

        $this->calculateTotals();
    }

    /* =======================
     * TOTAL CALCULATION
     * ======================= */
    public function calculateTotals()
    {
        $this->berat_loin = 0;
        $this->pcs_loin = count($this->rows);

        $this->berat_rm = [0, 0, 0];
        $this->pcs_rm = [0, 0, 0];

        $this->berat_hs = [0, 0, 0];
        $this->pcs_hs = [0, 0, 0];

        foreach ($this->rows as $row) {
            $this->berat_loin += (float)$row['berat_loin'];

            for ($i = 1; $i <= 3; $i++) {
                $rm = (float)$row['rm_berat_' . $i];
                $hs = (float)$row['hs_berat_' . $i];

                $this->berat_rm[$i - 1] += $rm;
                $this->berat_hs[$i - 1] += $hs;

                if ($rm > 0) $this->pcs_rm[$i - 1]++;
                if ($hs > 0) $this->pcs_hs[$i - 1]++;
            }
        }
    }

    /* =======================
     * SAVE DATA
     * ======================= */
    public function saveAll()
    {
        $this->validate([
            'session_tggl_cutting' => 'required|date',
            'session_tggl_injek_co' => 'required|date|after_or_equal:session_tggl_cutting',
            'session_tggl_service' => 'required|date|after_or_equal:session_tggl_injek_co',
            'penerimaan_id' => 'required',
        ]);

        if (empty($this->noBatch)) {
            session()->flash('error', 'No Batch tidak boleh kosong');
            return;
        }

        DB::beginTransaction();

        try {
            CuttingL::where('tggl_cutting', $this->session_tggl_cutting)
                ->where('tggl_injek_co', $this->session_tggl_injek_co)
                ->where('tggl_service', $this->session_tggl_service)
                ->where('penerimaan_id', $this->penerimaan_id)
                ->where('no_batch', $this->noBatch)
                ->where('grade_size_id', $this->selectedSizingLoin)
                ->delete();

            foreach ($this->rows as $row) {
                CuttingL::create([
                    'tggl_cutting' => $this->session_tggl_cutting,
                    'tggl_injek_co' => $this->session_tggl_injek_co,
                    'tggl_service' => $this->session_tggl_service,
                    'penerimaan_id' => $this->penerimaan_id,

                    'no_batch' => $this->noBatch,
                    'grade_size_id' => $this->selectedSizingLoin,
                    'rm_grade_1' => $this->selectedGradingService1,
                    'rm_grade_2' => $this->selectedGradingService2,
                    'rm_grade_3' => $this->selectedGradingService3,
                    'hs_grade_1' => $this->selectedGradingHService1,
                    'hs_grade_2' => $this->selectedGradingHService2,
                    'hs_grade_3' => $this->selectedGradingHService3,

                    'no_loin' => $row['no_loin'],
                    'berat_loin' => $row['berat_loin'],
                    'suhu_loin' => $row['suhu_loin'],

                    'rm_berat_1' => $row['rm_berat_1'] ?? 0,
                    'rm_berat_2' => $row['rm_berat_2'] ?? 0,
                    'rm_berat_3' => $row['rm_berat_3'] ?? 0,

                    'hs_berat_1' => $row['hs_berat_1'] ?? 0,
                    'hs_berat_2' => $row['hs_berat_2'] ?? 0,
                    'hs_berat_3' => $row['hs_berat_3'] ?? 0,
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

    /* =======================
     * RENDER
     * ======================= */
    public function render()
    {
        return view('livewire.cuttingl', [
            'penerimaan_ikan' => $this->penerimaan_ikan,
            'sizingLoin' => $this->sizingLoin,
            'gradingService' => $this->gradingService,
            'gradingHservice' => $this->gradingHservice,
        ]);
    }

    public function print()
    {
        $penerimaan = PenerimaanIkan::find($this->penerimaan_id);
        $pdf = Pdf::loadView(
            'pdf.cutting_by_l',
            [
                'tgl_cutting' => $this->session_tggl_cutting,
                'tgl_injek_co' => $this->session_tggl_injek_co,
                'tgl_service' => $this->session_tggl_service,
                'jenis_penerimaan' => $penerimaan?->jenis_penerimaan ?? 'N/A',
                'supplier' => $penerimaan?->supplier?->nama_supplier ?? 'N/A',
                'no_batch' => $this->noBatch,
                'selected_sizing_loin' => GradeL::find($this->selectedSizingLoin)?->grade_sizing ?? 'N/A',
                'grading_services' => [
                    GradeService::find($this->selectedGradingService1)?->grading ?? 'N/A',
                    GradeService::find($this->selectedGradingService2)?->grading ?? 'N/A',
                    GradeService::find($this->selectedGradingService3)?->grading ?? 'N/A',
                ],
                'grading_h_services' => [
                    GradeHService::find($this->selectedGradingHService1)?->grade_servicehs ?? 'N/A',
                    GradeHService::find($this->selectedGradingHService2)?->grade_servicehs ?? 'N/A',
                    GradeHService::find($this->selectedGradingHService3)?->grade_servicehs ?? 'N/A',
                ],
                'data' => $this->rows,
                'berat_loin' => $this->berat_loin,
                'berat_rm' => $this->berat_rm,
                'berat_hs' => $this->berat_hs,
                'pcs_loin' => $this->pcs_loin,
                'pcs_rm' => $this->pcs_rm,
                'pcs_hs' => $this->pcs_hs,
            ]
        );
        $pdf->setPaper('A4', 'landscape');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            'cutting_by_l_' . now()->format('Ymd_His') . '.pdf'
        );
    }
}
