<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KeluargaJemaat extends Model
{
    protected $table = 'keluarga_jemaat';

    protected $fillable = [
        'jemaat_id',
        'nama',
        'hubungan',
        'no_telp',
        'tanggal_lahir',
    ];

    public function jemaat(): BelongsTo
    {
        return $this->belongsTo(Jemaat::class);
    }
}
