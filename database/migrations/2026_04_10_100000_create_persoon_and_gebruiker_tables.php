<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('Persoon')) {
            Schema::create('Persoon', function (Blueprint $table) {
                $table->id('Id');
                $table->unsignedBigInteger('GezinId')->nullable();
                $table->string('Voornaam', 100);
                $table->string('Tussenvoegsel', 50)->nullable();
                $table->string('Achternaam', 100);
                $table->date('Geboortedatum');
                $table->string('TypePersoon', 50);
                $table->boolean('IsVertegenwoordiger')->default(false);
                $table->boolean('IsActief')->default(true);
                $table->string('Opmerking', 255)->nullable();
                $table->timestamp('DatumAangemaakt')->useCurrent();
                $table->timestamp('DatumGewijzigd')->useCurrent()->useCurrentOnUpdate();
            });
        }

        if (! Schema::hasTable('Gebruiker')) {
            Schema::create('Gebruiker', function (Blueprint $table) {
                $table->id('Id');
                $table->unsignedBigInteger('PersoonId');
                $table->string('InlogNaam', 100);
                $table->string('Gebruikersnaam', 150);
                $table->string('Wachtwoord', 255);
                $table->boolean('IsIngelogd')->default(false);
                $table->timestamp('Ingelogd')->nullable();
                $table->timestamp('Uitgelogd')->nullable();
                $table->boolean('IsActief')->default(true);
                $table->string('Opmerking', 255)->nullable();
                $table->timestamp('DatumAangemaakt')->useCurrent();
                $table->timestamp('DatumGewijzigd')->useCurrent()->useCurrentOnUpdate();

                $table->unique('PersoonId', 'uk_gebruiker_persoon');
                $table->unique('Gebruikersnaam', 'uk_gebruiker_gebruikersnaam');
                $table->foreign('PersoonId', 'fk_gebruiker_persoon')->references('Id')->on('Persoon')->cascadeOnDelete();
            });
        }

        if (! app()->environment('testing') && Schema::hasTable('Gebruiker') && DB::table('Gebruiker')->count() === 0) {
            $now = now();

            $persoonIds = [];
            $personen = [
                ['Voornaam' => 'Hans', 'Tussenvoegsel' => 'van', 'Achternaam' => 'Leeuwen', 'Geboortedatum' => '1958-02-12', 'TypePersoon' => 'Manager'],
                ['Voornaam' => 'Jan', 'Tussenvoegsel' => 'van der', 'Achternaam' => 'Sluijs', 'Geboortedatum' => '1993-04-30', 'TypePersoon' => 'Medewerker'],
                ['Voornaam' => 'Herman', 'Tussenvoegsel' => 'den', 'Achternaam' => 'Duiker', 'Geboortedatum' => '1989-08-30', 'TypePersoon' => 'Vrijwilliger'],
            ];

            foreach ($personen as $persoon) {
                $persoonIds[] = DB::table('Persoon')->insertGetId([
                    ...$persoon,
                    'IsVertegenwoordiger' => 0,
                    'IsActief' => 1,
                    'DatumAangemaakt' => $now,
                    'DatumGewijzigd' => $now,
                ], 'Id');
            }

            DB::table('Gebruiker')->insert([
                [
                    'PersoonId' => $persoonIds[0],
                    'InlogNaam' => 'Hans',
                    'Gebruikersnaam' => 'hans@maaskantje.nl',
                    'Wachtwoord' => '$2y$10$hvjzTDk/Ns7Oxa10Bu/ZmeYrffuj4MRAkczvAV9mwFbSw0HM6usgS',
                    'IsIngelogd' => 0,
                    'IsActief' => 1,
                    'DatumAangemaakt' => $now,
                    'DatumGewijzigd' => $now,
                ],
                [
                    'PersoonId' => $persoonIds[1],
                    'InlogNaam' => 'Jan',
                    'Gebruikersnaam' => 'jan@maaskantje.nl',
                    'Wachtwoord' => '$2y$10$hvjzTDk/Ns7Oxa10Bu/ZmeYrffuj4MRAkczvAV9mwFbSw0HM6usgS',
                    'IsIngelogd' => 0,
                    'IsActief' => 1,
                    'DatumAangemaakt' => $now,
                    'DatumGewijzigd' => $now,
                ],
                [
                    'PersoonId' => $persoonIds[2],
                    'InlogNaam' => 'Herman',
                    'Gebruikersnaam' => 'herman@maaskantje.nl',
                    'Wachtwoord' => '$2y$10$hvjzTDk/Ns7Oxa10Bu/ZmeYrffuj4MRAkczvAV9mwFbSw0HM6usgS',
                    'IsIngelogd' => 0,
                    'IsActief' => 1,
                    'DatumAangemaakt' => $now,
                    'DatumGewijzigd' => $now,
                ],
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Gebruiker');
        Schema::dropIfExists('Persoon');
    }
};

