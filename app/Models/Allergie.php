<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Allergie extends Model
{
    protected $table = 'Allergie';

    protected $primaryKey = 'Id';

    public $timestamps = false;

    protected $fillable = [
        'Naam',
        'Omschrijving',
        'AnafylactischRisico',
        'IsActief',
        'Opmerking',
    ];

    /**
     * Haal alle actieve allergieen op voor de dropdown via stored procedure.
     */
    public static function overzichtAllergieen(): array
    {
        return DB::select('CALL sp_allergie_overzicht_allergieen()');
    }
}
