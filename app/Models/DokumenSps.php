<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DokumenSps extends Model
{
    use HasFactory;

    protected $table = 'dokumen_sps';
    protected $guarded = [];

    public function berkas()
    {
        return $this->belongsTo(Berkas::class);
    }
}