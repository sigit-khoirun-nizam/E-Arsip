<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArchiveRack extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'location',
        'capacity',
        'status',
        'managed_by_unit_id',
        'created_by',
    ];

    public function archiveBoxes()
    {
        return $this->hasMany(ArchiveBox::class, 'archive_rack_id');
    }

    public function managedByUnit()
    {
        return $this->belongsTo(Unit::class, 'managed_by_unit_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
