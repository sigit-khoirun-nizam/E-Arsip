<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArchiveBox extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'archive_rack_id',
        'status',
        'managed_by_unit_id',
        'created_by',
    ];

    public function archiveRack()
    {
        return $this->belongsTo(ArchiveRack::class, 'archive_rack_id');
    }

    public function ordners()
    {
        return $this->hasMany(Ordner::class, 'archive_box_id');
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
