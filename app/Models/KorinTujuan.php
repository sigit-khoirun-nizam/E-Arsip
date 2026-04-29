<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KorinTujuan extends Model
{
    use HasFactory;

    protected $fillable = [
        'korin_id',
        'unit_tujuan_id',
        'status_baca',
    ];

    public function korin()
    {
        return $this->belongsTo(Korin::class);
    }

    public function unit()
    {
        return $this->belongsTo(\App\Models\Unit::class, 'unit_tujuan_id');
    }
}
