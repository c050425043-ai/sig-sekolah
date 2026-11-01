<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Memanggil SekolahSeeder agar data contoh sekolah terisi
        $this->call(SekolahSeeder::class);
    }
}