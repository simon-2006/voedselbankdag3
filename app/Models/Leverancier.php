<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leverancier extends Model
{
    use HasFactory;

    // 1. Koppel het model expliciet aan de juiste tabelnaam
    protected $table = 'Leverancier';

    // 2. Definieer de custom Primary Key (hoofdletter 'I')
    protected $primaryKey = 'Id';

    // 3. Pas de standaard timestamps aan naar de systeemvelden uit de casus
    const CREATED_AT = 'DatumAangemaakt';
    const UPDATED_AT = 'DatumGewijzigd';

    // 4. Bepaal welke velden we mogen vullen via mass-assignment (bijv. bij een create of update)
    protected $fillable = [
        'Naam',
        'ContactPersoon',
        'LeverancierNummer',
        'LeverancierType',
        'IsActief',
        'Opmerking'
    ];

    /**
     * RELATIES (Optioneel, maar heel handig voor de toekomst!)
     * * In plaats van complexe joins in je controller te schrijven, 
     * kun je met Eloquent relaties data veel makkelijker ophalen.
     */

    // Een leverancier heeft meerdere contactgegevens via de koppeltabel 'ContactPerLeverancier'
    public function contacten()
    {
        return $this->belongsToMany(
            Contact::class, 
            'ContactPerLeverancier', // Naam van de koppeltabel
            'LeverancierId',         // Foreign key van dit model in de koppeltabel
            'ContactId'              // Foreign key van het gerelateerde model in de koppeltabel
        );
    }

    // Een leverancier levert meerdere producten via de koppeltabel 'ProductPerLeverancier'
    public function producten()
    {
        return $this->belongsToMany(
            Product::class, 
            'ProductPerLeverancier', 
            'LeverancierId', 
            'ProductId'
        )->withPivot('Datum Aangeleverd', 'Datum Eerst VolgendeLevering'); 
        // withPivot zorgt ervoor dat je ook de extra velden in de koppeltabel kunt uitlezen
    }
}