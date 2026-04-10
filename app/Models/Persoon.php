<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    /**
     * Haal de huidige allergie-gegevens van 1 persoon op via stored procedure.
     */
    public static function huidigeAllergieGegevens(int $persoonId): ?object
    {
        $rows = DB::select('CALL sp_allergie_persoon_huidige_allergie(?)', [$persoonId]);

        return $rows[0] ?? null;
    }
}
