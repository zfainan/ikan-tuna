<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceL extends Model
{
    use HasFactory;

    protected $table = 'loin_services';
    protected $primaryKey = 'loin_service_id';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'tgl_service',
        'penerimaan_id',
        'cuttingl_id',
        'kategori_produk_id',
        'kode_lot',
        'berat_loin',
        'pcs_loin',

        // Col 1-7
        'kategori_1',
        'berat_1',
        'pcs_1',
        'kategori_2',
        'berat_2',
        'pcs_2',
        'kategori_3',
        'berat_3',
        'pcs_3',
        'kategori_4',
        'berat_4',
        'pcs_4',
        'kategori_5',
        'berat_5',
        'pcs_5',
        'kategori_6',
        'berat_6',
        'pcs_6',
        'kategori_7',
        'berat_7',
        'pcs_7',
    ];

    public function penerimaan()
    {
        return $this->belongsTo(PenerimaanIkan::class, 'penerimaan_id');
    }

        public function cuttingl()
    {
        return $this->belongsTo(CuttingL::class, 'cuttingl_id');
    }

    public function kategori_produk()
    {
        return $this->belongsTo(KategoriProduk::class, 'kategori_produk_id');
    }
}