<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArchiveShelf extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'location',
        'status',
        'managed_by_unit_id',
        'created_by',
    ];

    public function managedByUnit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'managed_by_unit_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function ordners()
    {
        return $this->hasMany(Ordner::class);
    }
}
