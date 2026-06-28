<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kecamatan extends Model
{
    protected $guarded = [];

    // Ini adalah fungsi yang dicari oleh sistem
    public function desa()
    {
        return $this->hasMany(Desa::class);
    }
}