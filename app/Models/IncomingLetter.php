<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class IncomingLetter extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'no_surat',
        'tanggal',
        'pengirim_id',
        'referensi',
        'tentang',
        'dokumen',
        'status',
        'disposisi',
    ];

    public function pengirim()
    {
        return $this->belongsTo(Pengirim::class);
    }
}
