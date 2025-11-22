<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PenerimaanIkan;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'suppliers'; // tabel
    protected $primaryKey = 'supplier_id'; // pakai supplier_id
    public $incrementing = true; // bukan auto increment
    protected $keyType = 'int'; // kalau supplier_id berupa angka

    protected $fillable = [
        'supplier_id',
        'nama_supplier',
        'alamat',
    ];

    public function penerimaan_ikan()
    {
        return $this->hasMany(PenerimaanIkan::class, 'supplier_id', 'supplier_id')
        ->withTrashed();
    }

    public function getRouteKeyName()
    {
        return 'supplier_id';
    }
}
