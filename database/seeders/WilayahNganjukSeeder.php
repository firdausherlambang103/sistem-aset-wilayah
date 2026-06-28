<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kecamatan;
use App\Models\Desa;

class WilayahNganjukSeeder extends Seeder
{
    public function run(): void
    {
        $wilayah = [
            'Bagor' => ['Bagor Wetan', 'Bagor Kulon', 'Ngumpul', 'Petak', 'Sekarputih'],
            'Baron' => ['Baron', 'Jambean', 'Kemlokolegi', 'Sambiroto', 'Garung'],
            'Berbek' => ['Berbek', 'Sukun', 'Gading', 'Cengkok', 'Bulu'],
            'Gondang' => ['Gondang', 'Balonggebang', 'Losari', 'Sanggrahan', 'Karangsemi'],
            'Jatikalen' => ['Jatikalen', 'Dawuhan', 'Pulorejo', 'Gondangwetan', 'Munung'],
            'Kertosono' => ['Kertosono', 'Banaran', 'Kepanjen', 'Drenges', 'Ngepeh'],
            'Lengkong' => ['Lengkong', 'Ketandan', 'Ngringin', 'Pinggir', 'Sawahan'],
            'Loceret' => ['Loceret', 'Patihan', 'Genjeng', 'Sukorejo', 'Gejagan'],
            'Nganjuk' => ['Mangundikaran', 'Payaman', 'Ringinanom', 'Kauman', 'Ploso'],
            'Ngetos' => ['Ngetos', 'Kepel', 'Blongko', 'Klodan', 'Tritik'],
            'Ngluyu' => ['Ngluyu', 'Sugihwaras', 'Bajulan', 'Gampeng', 'Lengkonglor'],
            'Ngronggot' => ['Ngronggot', 'Tanjok', 'Kelutan', 'Mojokerep', 'Cengkok'],
            'Pace' => ['Pace Wetan', 'Pace Kulon', 'Cerme', 'Batembat', 'Kecubung'],
            'Patianrowo' => ['Patianrowo', 'Bukur', 'Ngepung', 'Pisang', 'Pabian'],
            'Prambon' => ['Prambon', 'Gondanglegi', 'Sugihwaras', 'Singkalanyar', 'Wonoasri'],
            'Rejoso' => ['Rejoso', 'Setren', 'Mungkung', 'Jatirejo', 'Sidokare'],
            'Sawahan' => ['Sawahan', 'Kebonagung', 'Bendolo', 'Ngliman', 'Duren'],
            'Sukomoro' => ['Sukomoro', 'Nglundo', 'Kedunglo', 'Ngrengket', 'Bagor'],
            'Tanjunganom' => ['Warujayeng', 'Tanjungkalang', 'Malangsuko', 'Kedungombo', 'Ngadirejo'],
            'Wilangan' => ['Wilangan', 'Ngadipiro', 'Mancon', 'Sudimoroharjo', 'Sumberagung'],
        ];

        foreach ($wilayah as $kec => $desas) {
            $kecamatan = Kecamatan::create(['nama_kecamatan' => strtoupper($kec)]);
            foreach ($desas as $desa) {
                Desa::create([
                    'kecamatan_id' => $kecamatan->id,
                    'nama_desa' => strtoupper($desa)
                ]);
            }
        }
    }
}