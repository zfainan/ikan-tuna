<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenerimaanIkan extends Model
{
    use HasFactory;

    protected $table = 'penerimaan_ikans';

    protected $primaryKey = 'penerimaan_id';

    protected $fillable = [
        'penerimaan_id',
        'tgl_penerimaan',
        'tgl_bongkar',
        'supplier_id',
        'jenis_penerimaan',
        'grade_id',
        'kategori_berat_id',
        'no_bak',
        'berat_ikan',
        'suhu_ikan',
        'no_ikan',
    ];

    protected $dates = [
        'tgl_penerimaan',
        'tgl_bongkar',
        'created_at',
        'updated_at',
    ];

    public function grade()
    {
        return $this->belongsTo(Grade::class, 'grade_id', 'grade_id');
    }

    public function kategoriBeratPenerimaan()
    {
        return $this->belongsTo(KategoriBeratPenerimaan::class, 'kategori_berat_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'supplier_id');
    }
}
