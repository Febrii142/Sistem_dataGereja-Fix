<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'type',
        'description',
        'min_age',
        'max_age',
    ];

    public function members()
    {
        return $this->belongsToMany(Member::class, 'jemaat_categories');
    }
}
