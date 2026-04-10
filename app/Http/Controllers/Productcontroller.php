<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Leverancier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;

class ProductController extends Controller
{
    /**
     * Haal alle producten van een specifieke leverancier op via een Stored Procedure.
     * (Onderdeel van User Story 08 - Wireframe 03)
     * * Eis 1: Commentaar
     * Eis 5: Stored Procedures
     */
    public function getProductenByLeverancier($leverancierId)
    {
        // Eis 8: Security
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Eis 3: Try Catch
        try {
            // Haal eerst de leverancier op voor de weergave boven de tabel
            $leverancier = Leverancier::findOrFail($leverancierId);

            // Aanroepen van de Stored Procedure (Die de 4 verplichte tabellen joined)
            $producten = DB::select('CALL sp_GetProductenPerLeverancier(?)', [$leverancierId]);
            
            return view('product.index', compact('producten', 'leverancier'));

        } catch (Exception $e) {
            // Eis 11: Technische log
            Log::error("Fout bij ophalen producten voor leverancier {$leverancierId}: " . $e->getMessage());
            
            // Eis 12: Terugkoppeling
            return redirect()->route('leverancier.index')
                ->with('error_melding', 'Er is een technische fout opgetreden bij het ophalen van de producten.');
        }
    }

    /**
     * Toon het formulier om de houdbaarheidsdatum te wijzigen.
     * (Onderdeel van User Story 08 - Wireframe 04)
     */
    public function edit(Product $product)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        return view('product.edit', compact('product'));
    }

    /**
     * Verwerk de nieuwe houdbaarheidsdatum met de 7-dagen restrictie.
     * (Onderdeel van User Story 08 - Wireframe 05 & 06)
     * * Eis 6: Passende naamgeving
     */
    public function update(Request $request, Product $product)
    {
        // Eis 9: Server-side validatie
        $request->validate([
            'houdbaarheidsdatum' => 'required|date'
        ]);

        // Eis 3: Try Catch
        try {
            // Reken met Carbon voor datums
            $huidigeDatum = Carbon::parse($product->Houdbaarheidsdatum);
            $nieuweDatum = Carbon::parse($request->houdbaarheidsdatum);
            
// Bereken het verschil (false zorgt dat we in de toekomst én verleden kunnen kijken)
            $verschilInDagen = $huidigeDatum->diffInDays($nieuweDatum, false);

            // NIEUWE Business rule: Datum mag niet worden verkort (minder dan 0 dagen verschil)
            if ($verschilInDagen < 0) {
                // Eis 11: Technische log
                Log::warning("Gebruiker ID " . auth()->id() . " probeerde product ID {$product->Id} te verkorten.");

                return redirect()->back()
                    ->withInput()
                    ->with('error_melding', 'De houdbaarheidsdatum is niet gewijzigd')
                    ->with('error_detail', 'De houdbaarheidsdatum mag niet naar een eerdere datum worden gezet');
            }

            // BESTAANDE Business rule: max 7 dagen verlengen (Scenario 02)
            if ($verschilInDagen > 7) {
                // Eis 11: Technische log
                Log::warning("Gebruiker ID " . auth()->id() . " probeerde product ID {$product->Id} met meer dan 7 dagen te verlengen.");

                // Eis 12: Terugkoppeling acties (Foutmelding - Wireframe 06)
                return redirect()->back()
                    ->withInput()
                    ->with('error_melding', 'De houdbaarheidsdatum is niet gewijzigd')
                    ->with('error_detail', 'De houdbaarheidsdatum mag met maximaal 7 dagen worden verlengd');
            }

            // Scenario 01: Succesvol opslaan
            $product->Houdbaarheidsdatum = $nieuweDatum->format('Y-m-d');
            $product->save();

            // Eis 11: Technische log
            Log::info("Product ID {$product->Id} succesvol verlengd tot {$product->Houdbaarheidsdatum} door Gebruiker ID " . auth()->id());

            // Eis 12: Terugkoppeling acties (Succesmelding - Wireframe 05)
            return redirect()->route('product.edit', $product->Id)
                ->with('success_melding', 'De houdbaarheidsdatum is gewijzigd');

        } catch (Exception $e) {
            // Eis 11: Technische log
            Log::error("Systeemfout bij updaten product ID {$product->Id}: " . $e->getMessage());
            
            return redirect()->back()
                ->with('error_melding', 'Er is een onverwachte fout opgetreden. Probeer het later opnieuw.');
        }
    }
}