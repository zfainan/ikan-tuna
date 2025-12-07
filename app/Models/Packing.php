<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Packing extends Model
{
    protected $fillable = [
        'jumlah_pack',
        'tanggal',
    ];

    public function kategoriByProduk()
    {
        return $this->belongsTo(KategoriByprodukCt::class, 'kategori_byproduk_id');
    }

    public function kategoriProduk()
    {
        return $this->belongsTo(KategoriProduk::class, 'kategori_produk_id');
    }
}
