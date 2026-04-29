<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Korin extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nomor_surat',
        'tanggal_surat',
        'perihal',
        'isi',
        'unit_pengirim_id',
        'dibuat_oleh',
        'status',
        'file_path',
    ];

    public function unitPengirim()
    {
        return $this->belongsTo(\App\Models\Unit::class, 'unit_pengirim_id');
    }

    public function pembuat()
    {
        return $this->belongsTo(\App\Models\User::class, 'dibuat_oleh');
    }

    public function tujuans()
    {
        return $this->hasMany(KorinTujuan::class);
    }

    public function disposisis()
    {
        return $this->hasMany(KorinDisposisi::class);
    }
}
