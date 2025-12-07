<?php

namespace App\Livewire;

use App\Models\Packing as Model;
use Livewire\Component;
use Livewire\WithPagination;

class Packing extends Component
{
    use WithPagination;

    public ?string $tanggal = null;
    public ?int $kategoriByProdukId = null;
    public ?int $kategoriProdukId = null;
    public int $perPage = 10;

    protected $queryString = [
        'tanggal' => ['except' => ''],
        'kategoriByProdukId' => ['except' => ''],
        'kategoriProdukId' => ['except' => ''],
        'perPage' => ['except' => 10],
        'page' => ['except' => 1],
    ];

    public $kategoriByProduk = [];
    public $kategoriProduk = [];

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function updatedTanggal()
    {
        $this->resetPage();
    }

    public function updatedKategoriByProdukId()
    {
        $this->resetPage();
    }

    public function updatedKategoriProdukId()
    {
        $this->resetPage();
    }

    // Insialisasi data
    public function mount()
    {
        $this->kategoriByProduk = \App\Models\KategoriByprodukCt::all();
        $this->kategoriProduk = \App\Models\KategoriProduk::all();
    }

    // Method untuk filter data
    public function filterData()
    {
        $query = Model::with(['kategoriByProduk', 'kategoriProduk']);

        // Filter berdasarkan tanggal packing
        if ($this->tanggal) {
            $query->whereDate('tanggal', $this->tanggal);
        }

        // Filter berdasarkan jenis penerimaan
        if ($this->kategoriByProdukId) {
            $query->where('kategori_byproduk_id', $this->kategoriByProdukId);
        }

        // Filter berdasarkan jenis penerimaan
        if ($this->kategoriProdukId) {
            $query->where('kategori_produk_id', $this->kategoriProdukId);
        }

        return $query
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage)
            ->withQueryString();
    }

    public function render()
    {
        return view('livewire.packing', [
            'packings' => $this->filterData(),
        ]);
    }
}
