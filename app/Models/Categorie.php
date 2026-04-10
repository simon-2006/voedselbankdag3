<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categorie extends Model
{
    protected $table      = 'Categorie';
    protected $primaryKey = 'Id';

    protected $fillable = [
        'Naam',
        'Omschrijving',
        'IsActief',
        'Opmerking',
    ];

    protected $casts = [
        'IsActief' => 'boolean',
    ];

    // -------------------------------------------------------------------------
    // Relaties
    // -------------------------------------------------------------------------

    public function producten(): HasMany
    {
        return $this->hasMany(Product::class, 'CategorieId', 'Id');
    }
}
