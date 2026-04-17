<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnggotaKeluarga extends Model
{
    protected $table = 'anggota_keluarga';

    protected $fillable = [
        'kepala_keluarga_id',
        'jemaat_id',
        'hubungan_keluarga',
        'status',
    ];

    public function kepalaKeluarga(): BelongsTo
    {
        return $this->belongsTo(Jemaat::class, 'kepala_keluarga_id');
    }

    public function jemaat(): BelongsTo
    {
        return $this->belongsTo(Jemaat::class, 'jemaat_id');
    }
}
