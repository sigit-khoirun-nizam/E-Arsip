<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name', 'short_name', 'color', 'description'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function archives()
    {
        return $this->hasMany(Archive::class);
    }
}
