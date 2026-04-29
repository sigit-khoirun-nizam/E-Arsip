<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LetterCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_id',
        'kode_surat',
        'deskripsi',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
