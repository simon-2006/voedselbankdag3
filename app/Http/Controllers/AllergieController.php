<?php

namespace App\Http\Controllers;

use App\Models\Allergie;
use App\Models\AllergiePerPersoon;
use App\Models\Gezin;
use App\Models\Persoon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AllergieController extends Controller
{
    /**
     * Toon het overzicht van gezinnen met allergieen en het optionele filter.
     */
    public function index(Request $request): View
    {
        $geselecteerdeAllergieId = (int) $request->query('allergie_id', 0);
        $parameter = $geselecteerdeAllergieId > 0 ? $geselecteerdeAllergieId : null;

        $allergieen = Allergie::overzichtAllergieen();
        $gezinnen = Gezin::overzichtMetAllergie($parameter);

        return view('allergie.overzicht', [
            'allergieen' => $allergieen,
            'gezinnen' => $gezinnen,
            'geselecteerdeAllergieId' => $geselecteerdeAllergieId,
            // Alleen bij een actieve selectie tonen we de "geen resultaten" melding.
            'toonLegeMelding' => $geselecteerdeAllergieId > 0 && count($gezinnen) === 0,
        ]);
    }

    /**
     * Toon gezinsinformatie plus alle gezinsleden met hun allergieen.
     */
    public function showGezin(int $gezinId): View
    {
        $gezin = Gezin::samenvatting($gezinId);

        if ($gezin === null) {
            abort(404);
        }

        $personen = Gezin::personenMetAllergieen($gezinId);

        return view('allergie.gezin', [
            'gezin' => $gezin,
            'personen' => $personen,
        ]);
    }

    /**
     * Toon de wijzig-pagina voor de allergie van een specifiek gezinslid.
     */
    public function edit(int $gezinId, int $persoonId): View
    {
        $persoon = Persoon::huidigeAllergieGegevens($persoonId);

        if ($persoon === null) {
            abort(404);
        }

        // Extra check: persoon moet bij het opgevraagde gezin horen.
        if ((int) $persoon->GezinId !== $gezinId) {
            abort(404);
        }

        $allergieen = Allergie::overzichtAllergieen();
        $heeftHoogRisico = $this->heeftHoogAnafylactischRisico((string) ($persoon->AnafylactischRisico ?? ''));

        return view('allergie.wijzig', [
            'persoon' => $persoon,
            'allergieen' => $allergieen,
            'gezinId' => $gezinId,
            'heeftHoogRisico' => $heeftHoogRisico,
        ]);
    }

    /**
     * Verwerk de allergiewijziging en stuur terug naar de wijzig-pagina.
     */
    public function update(Request $request, int $gezinId, int $persoonId): RedirectResponse
    {
        $validated = $request->validate([
            'allergie_id' => ['required', 'integer', 'min:1'],
        ]);

        $persoon = Persoon::huidigeAllergieGegevens($persoonId);

        if ($persoon === null) {
            abort(404);
        }

        if ((int) $persoon->GezinId !== $gezinId) {
            abort(404);
        }

        // Als de gekozen allergie gelijk is aan de huidige, is er functioneel niets gewijzigd.
        if ((int) $persoon->AllergieId === (int) $validated['allergie_id']) {
            return redirect()
                ->route('allergie.edit', ['gezin' => $gezinId, 'persoon' => $persoonId])
                ->with('wijziging_niet_doorgvoerd', 'Allergie niet gewijzigd');
        }

        AllergiePerPersoon::wijzigVoorPersoon(
            $persoonId,
            $persoon->AllergieId,
            (int) $validated['allergie_id']
        );

        return redirect()
            ->route('allergie.edit', ['gezin' => $gezinId, 'persoon' => $persoonId])
            ->with('wijziging_doorgvoerd', 'De wijziging is doorgevoerd');
    }

    /**
     * Beoordeel risicowaarden zoals "Hoog" en "RedelijkHoog" op een veilige manier.
     */
    private function heeftHoogAnafylactischRisico(string $risico): bool
    {
        $normalised = strtolower(trim($risico));

        return str_contains($normalised, 'hoog');
    }
}
