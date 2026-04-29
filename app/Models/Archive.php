<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Archive extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'title',
        'description',
        'category_id',
        'ordner_id',
        'unit_id',

        'file_path',
        'uploaded_by',
        'status',
        'shelf_code',
        'pic_id',
        'is_confidential',
        'upload_date'
    ];

    protected $casts = [
        'is_confidential' => 'boolean',
        'upload_date' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function ordner()
    {
        return $this->belongsTo(Ordner::class);
    }



    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function pic()
    {
        return $this->belongsTo(User::class, 'pic_id');
    }
}
