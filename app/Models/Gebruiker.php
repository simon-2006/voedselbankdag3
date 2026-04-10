<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Gebruiker extends Authenticatable
{
    protected $table = 'Gebruiker';

    protected $primaryKey = 'Id';

    public $timestamps = false;

    protected $fillable = [
        'PersoonId',
        'InlogNaam',
        'Gebruikersnaam',
        'Wachtwoord',
        'IsIngelogd',
        'Ingelogd',
        'Uitgelogd',
        'IsActief',
        'Opmerking',
    ];

    protected $hidden = [
        'Wachtwoord',
    ];

    protected function casts(): array
    {
        return [
            'IsActief' => 'boolean',
            'IsIngelogd' => 'boolean',
            'Ingelogd' => 'datetime',
            'Uitgelogd' => 'datetime',
        ];
    }

    public function getAuthPassword(): string
    {
        return $this->Wachtwoord;
    }

    public function getNameAttribute(): string
    {
        return (string) $this->InlogNaam;
    }

    public function getEmailAttribute(): string
    {
        return (string) $this->Gebruikersnaam;
    }
}
