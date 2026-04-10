<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProductPerMagazijn extends Model
{
    protected $table      = 'ProductPerMagazijn';
    protected $primaryKey = 'Id';

    protected $fillable = [
        'ProductId',
        'MagazijnId',
        'Locatie',
        'IsActief',
        'Opmerking',
    ];

    protected $casts = [
        'IsActief' => 'boolean',
    ];

    // -------------------------------------------------------------------------
    // Stored procedure aanroepen
    // -------------------------------------------------------------------------

    public static function overzicht(?string $categorie): Collection
    {
        return collect(
            DB::select('CALL sp_overzicht_productvoorraden(?)', [$categorie])
        );
    }

    public static function details(int $id): ?object
    {
        $resultaat = DB::select('CALL sp_product_details(?)', [$id]);

        return $resultaat[0] ?? null;
    }

    public static function wijzig(int $id, array $data): void
    {
        DB::statement('CALL sp_wijzig_productvoorraad(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
            $id,
            $data['Productnaam'],
            $data['Barcode'],
            $data['Houdbaarheidsdatum'],
            $data['Status'],
            $data['MagazijnLocatie'],
            $data['Ontvangstdatum'],
            $data['Uitleveringsdatum'],
            $data['Aantal'],
            $data['Eenheid'],
        ]);
    }
}
