<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'member_id',
        'service_date',
        'hadir',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'service_date' => 'date',
            'hadir' => 'boolean',
        ];
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
