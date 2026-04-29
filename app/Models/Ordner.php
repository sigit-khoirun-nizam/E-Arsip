<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ordner extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_id',
        'category_id',
        'code',
        'period',
        'status',
        'description',
        'letter_type_id',
        'archive_box_id',
        'retention_expires_at',
    ];

    protected $casts = [
        'retention_expires_at' => 'date',
    ];

    public function isRetentionExpired(): bool
    {
        return $this->retention_expires_at && $this->retention_expires_at->isPast();
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function letterType()
    {
        return $this->belongsTo(LetterType::class);
    }

    public function archives()
    {
        return $this->hasMany(Archive::class);
    }

    public function archiveBox()
    {
        return $this->belongsTo(ArchiveBox::class, 'archive_box_id');
    }
}
