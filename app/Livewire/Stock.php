<?php

namespace App\Livewire;

use App\Models\PenerimaanIkan;
use App\Models\KategoriByprodukCt;
use App\Models\KategoriProduk;
use App\Models\Packing as PackingModel;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class Stock extends Component
{
    public $session_tgl_packing;
    public $incoming_total_berat = 0;
    public $incoming_total_pcs = 0;
    public $total_berat = 0;
    public $total_pcs = 0;
    public Collection|null $kategori_byproduk_ct;
    public Collection|null $kategori_produk;
    public $rows = [];

    // Insialisasi data
    public function mount()
    {
        // Inisialisasi variabel yang diperlukan
        $this->kategori_byproduk_ct = KategoriByprodukCt::all();
        $this->kategori_produk = KategoriProduk::all();

        $this->rows = [];

        // Inisialisasi session jika ada di URL
        if (request()->has('tgl_packing')) {
            $this->session_tgl_packing = request('tgl_packing');
        }

        $this->loadData();
    }

    // Fungsi untuk memuat data yang sudah ada di database
    public function loadData()
    {
        $this->rows = [];
        $this->kategori_byproduk_ct?->each(function ($kategori) {
            $incoming = null;
            if (! empty($this->session_tgl_packing)) {
                $incoming = PackingModel::where('tanggal', $this->session_tgl_packing)
                    ->where('kategori_byproduk_id', $kategori->kategori_byproduk_id)
                    ->whereNull('kategori_produk_id')
                    ->selectRaw('SUM(berat_produk) as total_berat, SUM(total_produk) as total_pcs')
                    ->first();
            }

            $total = PackingModel::whereNull('kategori_produk_id')
                ->where('kategori_byproduk_id', $kategori->kategori_byproduk_id)
                ->selectRaw('SUM(berat_produk) as total_berat, SUM(total_produk) as total_pcs')
                ->first();

            $this->rows[] = [
                'product' => $kategori->nama_produk,
                'incoming_berat' => $incoming?->total_berat ?? 0,
                'incoming_pcs' => $incoming?->total_pcs ?? 0,
                'total_berat' => $total->total_berat ?? 0,
                'total_pcs' => $total->total_pcs ?? 0,
            ];
        });


        $this->kategori_produk?->each(function ($kategori) {
            $incoming = null;
            if (! empty($this->session_tgl_packing)) {
                $incoming = PackingModel::where('tanggal', $this->session_tgl_packing)
                    ->where('kategori_produk_id', $kategori->kategori_produk_id)
                    ->whereNull('kategori_byproduk_id')
                    ->selectRaw('SUM(berat_produk) as total_berat, SUM(total_produk) as total_pcs')
                    ->first();
            }

            $total = PackingModel::whereNull('kategori_byproduk_id')
                ->where('kategori_produk_id', $kategori->kategori_produk_id)
                ->selectRaw('SUM(berat_produk) as total_berat, SUM(total_produk) as total_pcs')
                ->first();

            $this->rows[] = [
                'product' => $kategori->nama_produk,
                'incoming_berat' => $incoming->total_berat ?? 0,
                'incoming_pcs' => $incoming->total_pcs ?? 0,
                'total_berat' => $total->total_berat ?? 0,
                'total_pcs' => $total->total_pcs ?? 0,
            ];
        });

        $this->calculateTotals();
    }

    public function calculateTotals()
    {
        $this->incoming_total_berat = 0;
        $this->incoming_total_pcs = 0;
        $this->total_berat = 0;
        $this->total_pcs = 0;

        foreach ($this->rows as $row) {
            $this->incoming_total_berat += (float) ($row['incoming_berat'] ?? 0);
            $this->incoming_total_pcs += (int) ($row['incoming_pcs'] ?? 0);
            $this->total_berat += (float) ($row['total_berat'] ?? 0);
            $this->total_pcs += (int) ($row['total_pcs'] ?? 0);
        }
    }

    // Update method updatedSessionTglPacking untuk reset data jika tanggal berubah
    public function updatedSessionTglPacking($value)
    {
        $this->loadData();
    }

    public function render()
    {
        return view('livewire.stock');
    }
}
