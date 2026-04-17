<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Baptisan extends Model
{
    protected $table = 'baptisan';

    protected $fillable = [
        'jemaat_id',
        'tanggal_baptis',
        'tempat_baptis',
        'nama_pendeta',
        'catatan',
    ];

    public function jemaat(): BelongsTo
    {
        return $this->belongsTo(Jemaat::class, 'jemaat_id');
    }
}
