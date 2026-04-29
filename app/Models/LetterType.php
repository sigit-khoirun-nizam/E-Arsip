<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LetterType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'color', 'description', 'unit_id'];

    public function archives()
    {
        return $this->hasMany(Archive::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
