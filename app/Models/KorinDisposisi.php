<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KorinDisposisi extends Model
{
    use HasFactory;

    protected $fillable = [
        'korin_id',
        'dari_user_id',
        'ke_user_id',
        'catatan',
        'status',
        'tanggal_disposisi',
    ];

    public function korin()
    {
        return $this->belongsTo(Korin::class);
    }

    public function pengirim()
    {
        return $this->belongsTo(\App\Models\User::class, 'dari_user_id');
    }

    public function penerima()
    {
        return $this->belongsTo(\App\Models\User::class, 'ke_user_id');
    }

    protected static function booted()
    {
        static::saved(function ($disposisi) {
            if ($disposisi->korin) {
                if ($disposisi->status === 'Setuju') {
                    $disposisi->korin->update(['status' => 'Selesai']);
                } elseif ($disposisi->status === 'Ditolak') {
                    $disposisi->korin->update(['status' => 'Ditolak']);
                } elseif ($disposisi->status === 'Pending') {
                    $disposisi->korin->update(['status' => 'Pending']);
                }
            }
        });
    }
}
