<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $table      = 'Product';
    protected $primaryKey = 'Id';

    protected $fillable = [
        'CategorieId',
        'Naam',
        'SoortAllergie',
        'Barcode',
        'Houdbaarheidsdatum',
        'Omschrijving',
        'Status',
        'IsActief',
        'Opmerking',
    ];

    protected $casts = [
        'Houdbaarheidsdatum' => 'date',
        'IsActief'           => 'boolean',
    ];

    // -------------------------------------------------------------------------
    // Relaties
    // -------------------------------------------------------------------------

    public function categorie(): BelongsTo
    {
        return $this->belongsTo(Categorie::class, 'CategorieId', 'Id');
    }

    public function productPerMagazijn(): HasMany
    {
        return $this->hasMany(ProductPerMagazijn::class, 'ProductId', 'Id');
    }
}
