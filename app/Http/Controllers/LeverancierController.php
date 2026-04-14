<?php

namespace App\Http\Controllers;

use App\Models\Leverancier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class LeverancierController extends Controller
{
    /**
     * Toon het overzicht van leveranciers (User Story 07).
     * Eis 1: Commentaar
     * Eis 7: MVC Architectuur
     */
    public function index(Request $request)
    {
        // Eis 8: Security (Zorg dat de gebruiker is ingelogd)
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $types = ['Bedrijf', 'Instelling', 'Overheid', 'Particulier', 'Donor'];
        $selectedType = $request->query('leverancier_type');

        if (!in_array($selectedType, $types, true)) {
            $selectedType = null;
        }

        // Eis 3: Try Catch
        try {
            // Scenario 2 (US_07): Donor hoort geen leveranciers/producten te tonen in dit overzicht.
            if ($selectedType === 'Donor') {
                $leveranciers = collect();
                $toonLegeMelding = true;
            } else {
                // Eis 2: Joins (Verplicht onderdeel van het examen)
                $query = DB::table('Leverancier as l')
                    ->leftJoin('ContactPerLeverancier as cpl', 'cpl.LeverancierId', '=', 'l.Id')
                    ->leftJoin('Contact as c', 'c.Id', '=', 'cpl.ContactId')
                    ->where('l.IsActief', 1)
                    // Donor wordt in dit leveranciersoverzicht niet getoond.
                    ->where('l.LeverancierType', '!=', 'Donor')
                    ->select(
                        'l.Id',
                        'l.Naam',
                        'l.ContactPersoon',
                        'l.LeverancierNummer',
                        'l.LeverancierType',
                        DB::raw('MAX(c.Email) as Email'),
                        DB::raw('MAX(c.Mobiel) as Mobiel')
                    )
                    ->groupBy('l.Id', 'l.Naam', 'l.ContactPersoon', 'l.LeverancierNummer', 'l.LeverancierType')
                    ->orderBy('l.Naam');

                // Filter toepassen als er een type is geselecteerd
                if ($selectedType !== null) {
                    $query->where('l.LeverancierType', $selectedType);
                }

                $leveranciers = $query->get();
                $toonLegeMelding = ($selectedType !== null && $leveranciers->isEmpty());
            }

            return view('leverancier.index', [
                'leveranciers' => $leveranciers,
                'types' => $types,
                'geselecteerdeType' => $selectedType,
                'toonLegeMelding' => $toonLegeMelding,
                'feedbackType' => null,
                'feedbackMessage' => null,
                'overzichtBeschikbaar' => true,
            ]);

        } catch (Exception $e) {
            // Eis 11: Technische log
            Log::error('Fout bij ophalen leveranciers overzicht.', [
                'gebruiker_id' => auth()->id(),
                'leverancier_type' => $selectedType,
                'error' => $e->getMessage(),
            ]);

            // Scenario_03 (US_07): Technisch foutscenario met duidelijke terugkoppeling
            return response()->view('leverancier.index', [
                'leveranciers' => collect(),
                'types' => $types,
                'geselecteerdeType' => $selectedType,
                'toonLegeMelding' => false,
                'feedbackType' => 'danger',
                'feedbackMessage' => 'Het overzicht van leveranciers is tijdelijk niet beschikbaar. Probeer het later opnieuw.',
                'overzichtBeschikbaar' => false,
            ], 503);
        }
    }

    /**
     * Toon het formulier om een leverancier te wijzigen (Extra Functionaliteit).
     */
    public function edit(Leverancier $leverancier)
    {
        return view('leveranciers.edit', compact('leverancier'));
    }

    /**
     * Update de gegevens van een specifieke leverancier in de database.
     * Eis 6: Passende naamgeving
     */
    public function update(Request $request, Leverancier $leverancier)
    {
        // Eis 9: Server-side validatie
        $request->validate([
            'Naam' => 'required|string|max:255',
            'ContactPersoon' => 'required|string|max:255',
            'LeverancierNummer' => 'required|string|max:50',
            'LeverancierType' => 'required|string|in:Bedrijf,Instelling,Overheid,Particulier,Donor',
        ]);

        // Eis 3: Try Catch
        try {
            // Update de velden met behulp van Eloquent
            $leverancier->Naam = $request->input('Naam');
            $leverancier->ContactPersoon = $request->input('ContactPersoon');
            $leverancier->LeverancierNummer = $request->input('LeverancierNummer');
            $leverancier->LeverancierType = $request->input('LeverancierType');
            
            // Opslaan in de database
            $leverancier->save();

            // Eis 11: Technische log
            Log::info("Gebruiker ID " . auth()->id() . " heeft leverancier ID {$leverancier->Id} succesvol geüpdatet.");

            // Eis 12: Terugkoppeling acties d.m.v. meldingen
            return redirect()->route('leverancier.index')->with('status', 'De gegevens van de leverancier zijn succesvol gewijzigd!');

        } catch (Exception $e) {
            // Eis 11: Technische log bij een fout
            Log::error("Systeemfout bij updaten leverancier ID {$leverancier->Id}: " . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Er is een onverwachte fout opgetreden. Controleer de technische log voor details.']);
        }
    }
}
