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
        return $this->belongsTo(Kategori_produk::class, 'kategori_produk_id');
    }
}