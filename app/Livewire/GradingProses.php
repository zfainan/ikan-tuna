<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PenerimaanIkan;
use App\Models\Grade;
use Carbon\Carbon;

class GradingProses extends Component
{
    // Properties for filtering
    public $tanggalPenerimaan;
    public $tanggalBongkar;
    public $supplier;
    public $jenisPenerimaan;
    public $selectedPenerimaan = [];
    public $selectedGrade = [];
    
    // Properties for modal
    public $showGradingModal = false;
    public $penerimaanId;
    public $gradeId;
    public $keterangan;

    // Initialize with today's date
    public function mount()
    {
        $this->tanggalPenerimaan = Carbon::today()->toDateString();
    }

    // Update the list of fish receptions when filter changes
    public function updatedTanggalPenerimaan()
    {
        $this->reset(['tanggalBongkar', 'supplier', 'jenisPenerimaan', 'selectedPenerimaan']);
    }

    // Get filtered fish receptions
    public function getPenerimaanIkanProperty()
    {
        $query = PenerimaanIkan::with(['supplier', 'grade', 'kategori_berat_penerimaan'])
            ->whereDate('tgl_penerimaan', $this->tanggalPenerimaan);

        if ($this->tanggalBongkar) {
            $query->whereDate('tgl_bongkar', $this->tanggalBongkar);
        }

        if ($this->supplier) {
            $query->where('supplier_id', $this->supplier);
        }

        if ($this->jenisPenerimaan) {
            $query->where('jenis_penerimaan', $this->jenisPenerimaan);
        }

        return $query->get();
    }

    // Get all available grades
    public function getGradesProperty()
    {
        return Grade::all();
    }

    // Open grading modal
    public function openGradingModal($penerimaanId)
    {
        $this->penerimaanId = $penerimaanId;
        $penerimaan = PenerimaanIkan::find($penerimaanId);
        $this->gradeId = $penerimaan->grade_id;
        $this->showGradingModal = true;
    }

    // Save grade
    public function saveGrade()
    {
        $this->validate([
            'gradeId' => 'required|exists:grades,id',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $penerimaan = PenerimaanIkan::find($this->penerimaanId);
        $penerimaan->grade_id = $this->gradeId;
        $penerimaan->save();

        // Reset modal
        $this->reset(['showGradingModal', 'penerimaanId', 'gradeId', 'keterangan']);
        
        // Show success message
        session()->flash('message', 'Grade berhasil diperbarui.');
    }

    // Bulk grade update
    public function bulkUpdateGrade()
    {
        if (empty($this->selectedPenerimaan) || empty($this->selectedGrade)) {
            session()->flash('error', 'Pilih setidaknya satu penerimaan dan grade.');
            return;
        }

        PenerimaanIkan::whereIn('penerimaan_id', $this->selectedPenerimaan)
            ->update(['grade_id' => $this->selectedGrade]);

        $this->reset(['selectedPenerimaan', 'selectedGrade']);
        session()->flash('message', 'Grade berhasil diperbarui untuk item yang dipilih.');
    }

    public function render()
    {
        return view('livewire.grading-proses', [
            'penerimaanIkan' => $this->penerimaanIkan,
            'grades' => $this->grades,
        ]);
    }
}
