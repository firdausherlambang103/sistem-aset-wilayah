<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfilBpn extends Model
{
    use HasFactory;

    // Beritahu Laravel untuk menggunakan nama tabel ini secara spesifik
    protected $table = 'profil_bpn';

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}