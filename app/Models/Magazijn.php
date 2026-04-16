<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Magazijn extends Model
{
    protected $table      = 'Magazijn';
    protected $primaryKey = 'Id';

    protected $fillable = [
        'Ontvangstdatum',
        'Uitleveringsdatum',
        'VerpakkingsEenheid',
        'Aantal',
        'IsActief',
        'Opmerking',
    ];

    protected $casts = [
        'Ontvangstdatum'    => 'date',
        'Uitleveringsdatum' => 'date',
        'IsActief'          => 'boolean',
    ];

    // -------------------------------------------------------------------------
    // Relaties
    // -------------------------------------------------------------------------

    public function productPerMagazijn(): HasMany
    {
        return $this->hasMany(ProductPerMagazijn::class, 'MagazijnId', 'Id');
    }
}
