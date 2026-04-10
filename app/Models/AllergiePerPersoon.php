<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AllergiePerPersoon extends Model
{
    protected $table = 'AllergiePerPersoon';

    protected $primaryKey = 'Id';

    public $timestamps = false;

    protected $fillable = [
        'PersoonId',
        'AllergieId',
        'IsActief',
        'Opmerking',
    ];

    /**
     * Voer de wijziging van een persoonsallergie uit via stored procedure.
     */
    public static function wijzigVoorPersoon(int $persoonId, ?int $oudeAllergieId, int $nieuweAllergieId): void
    {
        DB::statement('CALL sp_allergie_wijzig_persoon(?, ?, ?)', [
            $persoonId,
            $oudeAllergieId,
            $nieuweAllergieId,
        ]);
    }
}
