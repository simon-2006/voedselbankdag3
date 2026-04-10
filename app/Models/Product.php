<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Koppel het model aan de exacte tabelnaam uit de testdata
    protected $table = 'Product';

    // Definieer de custom Primary Key (hoofdletter I)
    protected $primaryKey = 'Id';

    // Pas de standaard Laravel timestamps aan naar de verplichte systeemvelden uit de casus
    const CREATED_AT = 'DatumAangemaakt';
    const UPDATED_AT = 'DatumGewijzigd';

    // Bepaal welke velden we veilig mogen aanpassen via formulieren (Mass Assignment)
    protected $fillable = [
        'CategorieId',
        'Naam',
        'SoortAllergie',
        'Barcode',
        'Houdbaarheidsdatum',
        'Omschrijving',
        'Status',
        'IsActief',
        'Opmerking'
    ];

    /**
     * RELATIES (Eis 7: MVC Architectuur / Datamodellering)
     */
    
    // Een product wordt geleverd door meerdere leveranciers via de koppeltabel 'ProductPerLeverancier'
    public function leveranciers()
    {
        return $this->belongsToMany(
            Leverancier::class, 
            'ProductPerLeverancier', // Naam van de koppeltabel
            'ProductId',             // Foreign key van dit model in de koppeltabel
            'LeverancierId'          // Foreign key van het andere model in de koppeltabel
        );
    }
}