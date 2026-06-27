<?php
// app/Models/User.php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = ['email', 'password', 'role', 'is_approved'];
    protected $hidden = ['password', 'remember_token'];

    public function profilBpn() {
        return $this->hasOne(ProfilBpn::class);
    }

    public function profilMitra() {
        return $this->hasOne(ProfilMitra::class);
    }

    public function berkas() {
        return $this->hasMany(Berkas::class, 'mitra_id');
    }
}