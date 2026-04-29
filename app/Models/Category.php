<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name', 'description', 'retention_years', 'unit_id'];

    public function archives()
    {
        return $this->hasMany(Archive::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function ordners()
    {
        return $this->hasMany(Ordner::class);
    }
}
