<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutgoingLetter extends Model
{
    use HasFactory;

    protected $fillable = [
        'tanggal',
        'nomor_surat',
        'unit_id',
        'letter_category_id',
        'kepada',
        'perihal',
        'referensi',
        'file_path',
        'keterangan',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function letterCategory()
    {
        return $this->belongsTo(LetterCategory::class);
    }
}
