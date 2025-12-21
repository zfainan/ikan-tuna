<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CuttingL extends Model
{
    use HasFactory;

    protected $table = 'cuttingls';
    protected $primaryKey = 'cuttingl_id';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'tggl_cutting',
        'tggl_injek_co',
        'tggl_service',
        'penerimaan_id',
        'no_batch',
        'grade_size_id',
        'no_loin',
        'berat_loin',
        'suhu_loin',
        'pcs_loin',
        'grade_service_id',
        'berat_rm',
        'pcs_rm',
        'grade_servicehs_id',
        'berat_hs',
        'pcs_hs',

        // RM
        'rm_grade_1',
        'rm_grade_2',
        'rm_grade_3',
        'rm_berat_1',
        'rm_berat_2',
        'rm_berat_3',
        // HS
        'hs_grade_1',
        'hs_grade_2',
        'hs_grade_3',
        'hs_berat_1',
        'hs_berat_2',
        'hs_berat_3',
    ];

    public function grade_size()
    {
        return $this->belongsTo(GradeL::class, 'grade_size_id');
    }

    public function penerimaan()
    {
        return $this->belongsTo(PenerimaanIkan::class, 'penerimaan_id');
    }

    public function grade_service()
    {
        return $this->belongsTo(GradeService::class, 'grade_service_id');
    }

    public function grade_servicehs()
    {
        return $this->belongsTo(GradeHService::class, 'grade_servicehs_id');
    }
}
