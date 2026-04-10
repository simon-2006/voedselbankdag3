<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Gezin extends Model
{
    protected $table = 'Gezin';

    protected $primaryKey = 'Id';

    public $timestamps = false;

    protected $fillable = [
        'Naam',
        'Code',
        'Omschrijving',
        'AantalVolwassenen',
        'AantalKinderen',
        'AantalBabys',
        'TotaalAantalPersonen',
        'IsActief',
        'Opmerking',
    ];

    /**
     * Geef gezinnen terug met een optioneel allergiefilter.
     */
    public static function overzichtMetAllergie(?int $allergieId): array
    {
        return DB::select('CALL sp_allergie_overzicht_gezinnen(?)', [$allergieId]);
    }

    /**
     * Geef de samenvatting van 1 gezin terug.
     */
    public static function samenvatting(int $gezinId): ?object
    {
        $rows = DB::select('CALL sp_allergie_gezin_samenvatting(?)', [$gezinId]);

        return $rows[0] ?? null;
    }

    /**
     * Geef alle personen van een gezin met allergie-informatie terug.
     */
    public static function personenMetAllergieen(int $gezinId): array
    {
        return DB::select('CALL sp_allergie_gezin_personen(?)', [$gezinId]);
    }
}
