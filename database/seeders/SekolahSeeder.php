<?php

namespace Database\Seeders;

use App\Models\Sekolah;
use Illuminate\Database\Seeder;

class SekolahSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'npsn' => '30304256',
                'nama_sekolah' => 'SDN Melayu 2',
                'jenjang' => 'SD',
                'status' => 'Negeri',
                'alamat' => 'Jl. Veteran No. 41',
                'kecamatan' => 'Banjarmasin Timur',
                'kabupaten_kota' => 'Kota Banjarmasin',
                'latitude' => -3.32185240,
                'longitude' => 114.60198260,
            ],
            [
                'npsn' => '30304123',
                'nama_sekolah' => 'SMP Negeri 1 Banjarmasin',
                'jenjang' => 'SMP',
                'status' => 'Negeri',
                'alamat' => 'Jl. Kesatriaan No. 1',
                'kecamatan' => 'Banjarmasin Timur',
                'kabupaten_kota' => 'Kota Banjarmasin',
                'latitude' => -3.32350000,
                'longitude' => 114.60300000,
            ],
            [
                'npsn' => '30304999',
                'nama_sekolah' => 'SMA Negeri 1 Banjarmasin',
                'jenjang' => 'SMA',
                'status' => 'Negeri',
                'alamat' => 'Jl. Mulawarman No. 25',
                'kecamatan' => 'Banjarmasin Tengah',
                'kabupaten_kota' => 'Kota Banjarmasin',
                'latitude' => -3.31800000,
                'longitude' => 114.59200000,
            ],
        ];

        foreach ($data as $item) {
            Sekolah::updateOrCreate(['npsn' => $item['npsn']], $item);
        }
    }
}