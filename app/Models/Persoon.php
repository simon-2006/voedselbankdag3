<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Persoon extends Model
{
    protected $table = 'Persoon';

    protected $primaryKey = 'Id';

    public $timestamps = false;

    protected $fillable = [
        'GezinId',
        'Voornaam',
        'Tussenvoegsel',
        'Achternaam',
        'Geboortedatum',
        'TypePersoon',
        'IsVertegenwoordiger',
        'IsActief',
        'Opmerking',
    ];
}
