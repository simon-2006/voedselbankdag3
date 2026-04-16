<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Auth loopt via de tabel `Gebruiker`.
        // Standaardaccounts worden aangemaakt via de Gebruiker-migratie indien nodig.
    }
}
