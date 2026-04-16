<?php

use App\Models\Gebruiker;
use App\Models\Persoon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('can register a new gebruiker', function () {
    $response = $this->post('/registreren', [
        'name' => 'Test Gebruiker',
        'email' => 'test@example.com',
        'password' => 'wachtwoord123',
        'password_confirmation' => 'wachtwoord123',
    ]);

    $response->assertRedirect('/dashboard');
    $this->assertAuthenticated();
    $this->assertDatabaseHas('Gebruiker', [
        'Gebruikersnaam' => 'test@example.com',
    ]);
});

it('can log in and log out', function () {
    $persoon = Persoon::create([
        'Voornaam' => 'Test',
        'Achternaam' => 'Gebruiker',
        'Geboortedatum' => '1990-01-01',
        'TypePersoon' => 'Vrijwilliger',
        'IsVertegenwoordiger' => 0,
        'IsActief' => 1,
    ]);

    $gebruiker = Gebruiker::create([
        'PersoonId' => $persoon->Id,
        'InlogNaam' => 'Test Gebruiker',
        'Gebruikersnaam' => 'bestaat@example.com',
        'Wachtwoord' => Hash::make('wachtwoord123'),
        'IsActief' => 1,
    ]);

    $login = $this->post('/inloggen', [
        'email' => 'bestaat@example.com',
        'password' => 'wachtwoord123',
    ]);

    $login->assertRedirect('/dashboard');
    $this->assertAuthenticatedAs($gebruiker);

    $logout = $this->post('/uitloggen');
    $logout->assertRedirect('/');
    $this->assertGuest();
});

it('rejects invalid login credentials', function () {
    $persoon = Persoon::create([
        'Voornaam' => 'Bestaat',
        'Achternaam' => 'Gebruiker',
        'Geboortedatum' => '1991-01-01',
        'TypePersoon' => 'Vrijwilliger',
        'IsVertegenwoordiger' => 0,
        'IsActief' => 1,
    ]);

    Gebruiker::create([
        'PersoonId' => $persoon->Id,
        'InlogNaam' => 'Bestaat Gebruiker',
        'Gebruikersnaam' => 'bestaat@example.com',
        'Wachtwoord' => Hash::make('correct123'),
        'IsActief' => 1,
    ]);

    $response = $this->from('/inloggen')->post('/inloggen', [
        'email' => 'bestaat@example.com',
        'password' => 'foutwachtwoord',
    ]);

    $response->assertRedirect('/inloggen');
    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});
