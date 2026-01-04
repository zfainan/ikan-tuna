<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Packing extends Model
{
    protected $fillable = [
        'penerimaan_id',
        'kategori_byproduk_id',
        'kategori_produk_id',
        'berat_produk',
        'total_produk',
        'tanggal',
    ];

    protected $casts = [
        'berat_produk' => 'decimal:2',
    ];

    public function kategoriByProduk()
    {
        return $this->belongsTo(KategoriByprodukCt::class, 'kategori_byproduk_id');
    }

    public function kategoriProduk()
    {
        return $this->belongsTo(KategoriProduk::class, 'kategori_produk_id');
    }

    public function penerimaan()
    {
        return $this->belongsTo(PenerimaanIkan::class, 'penerimaan_id');
    }
}
