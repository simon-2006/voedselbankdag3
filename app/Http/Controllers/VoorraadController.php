<?php

namespace App\Http\Controllers;

use App\Models\ProductPerMagazijn;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class VoorraadController extends Controller
{
    // -------------------------------------------------------------------------
    // index – Overzicht productvoorraden
    // -------------------------------------------------------------------------
    public function index(Request $request): View
    {
        $gekozenCategorie  = $request->string('categorie')->trim()->toString();
        $categorieFilter   = $gekozenCategorie !== '' ? $gekozenCategorie : null;

        $categorieen       = collect();
        $voorraadProducten = collect();
        $foutmelding       = null;

        try {
            $categorieen = DB::table('Categorie')
                ->where('IsActief', 1)
                ->orderBy('Naam')
                ->pluck('Naam');

            $voorraadProducten = ProductPerMagazijn::overzicht($categorieFilter);
        } catch (\Throwable $exception) {
            $categorieen = $this->standaardCategorieen();
            $foutmelding = 'Er ging iets onverwachts mis bij het ophalen van de voorraad.';
        }

        return view('voorraad.index', [
            'categorieen'       => $categorieen,
            'voorraadProducten' => $voorraadProducten,
            'gekozenCategorie'  => $categorieFilter,
            'foutmelding'       => $foutmelding,
        ]);
    }

    // -------------------------------------------------------------------------
    // show – Product details
    // -------------------------------------------------------------------------
    public function show(int $id): View
    {
        $product     = null;
        $foutmelding = null;

        try {
            $product = ProductPerMagazijn::details($id);

            if ($product === null) {
                $foutmelding = 'Het product kon niet worden gevonden.';
            }
        } catch (\Throwable $exception) {
            $foutmelding = 'Er ging iets onverwachts mis bij het ophalen van de productdetails.';
        }

        return view('voorraad.show', [
            'product'     => $product,
            'foutmelding' => $foutmelding,
        ]);
    }

    // -------------------------------------------------------------------------
    // edit – Wijzig-formulier tonen
    // -------------------------------------------------------------------------
    public function edit(int $id): View
    {
        $product     = null;
        $foutmelding = null;

        try {
            $product = ProductPerMagazijn::details($id);

            if ($product === null) {
                $foutmelding = 'Het product kon niet worden gevonden.';
            }
        } catch (\Throwable $exception) {
            $foutmelding = 'Er ging iets onverwachts mis bij het ophalen van de productdetails.';
        }

        return view('voorraad.edit', [
            'product'     => $product,
            'locaties'    => $this->standaardLocaties(),
            'foutmelding' => $foutmelding,
        ]);
    }

    // -------------------------------------------------------------------------
    // update – Wijzigingen opslaan
    // -------------------------------------------------------------------------
    public function update(Request $request, int $id): View|RedirectResponse
    {
        $request->validate([
            'Productnaam'        => ['required', 'string', 'max:150'],
            'Houdbaarheidsdatum' => ['required', 'date'],
            'Barcode'            => ['required', 'string', 'max:20'],
            'MagazijnLocatie'    => ['required', 'string'],
            'Ontvangstdatum'     => ['required', 'date'],
            'AantalUitgeleverd'  => ['nullable', 'integer', 'min:0'],
            'Uitleveringsdatum'  => ['nullable', 'date'],
            'AantalOpVoorraad'   => ['required', 'integer', 'min:0'],
        ]);

        $aantalUitgeleverd = (int) $request->input('AantalUitgeleverd', 0);
        $aantalOpVoorraad  = (int) $request->input('AantalOpVoorraad');

        // Scenario_02: meer uitleveren dan voorraad.
        if ($aantalUitgeleverd > $aantalOpVoorraad) {
            return back()->withErrors([
                'AantalUitgeleverd' => 'Er worden meer producten uitgeleverd dan er in voorraad zijn. ',
            ])->withInput();
        }

        $nieuwAantal = $aantalOpVoorraad - $aantalUitgeleverd;

        try {
            // Haal huidige waarden op voor velden die niet in het formulier zitten.
            $huidig = ProductPerMagazijn::details($id);

            ProductPerMagazijn::wijzig($id, [
                'Productnaam'        => $request->input('Productnaam'),
                'Barcode'            => $request->input('Barcode'),
                'Houdbaarheidsdatum' => $request->input('Houdbaarheidsdatum'),
                'Status'             => $huidig->Status,
                'MagazijnLocatie'    => $request->input('MagazijnLocatie'),
                'Ontvangstdatum'     => $request->input('Ontvangstdatum'),
                'Uitleveringsdatum'  => $request->input('Uitleveringsdatum') ?: null,
                'Aantal'             => $nieuwAantal,
                'Eenheid'            => $huidig->Eenheid,
            ]);
        } catch (\Throwable $exception) {
            return back()->withErrors([
                'algemeen' => 'Er ging iets onverwachts mis bij het wijzigen van het product.',
            ])->withInput();
        }

        return view('voorraad.gewijzigd', [
            'productPerMagazijnId' => $id,
        ]);
    }

    // -------------------------------------------------------------------------
    // Hulpfuncties
    // -------------------------------------------------------------------------
    private function standaardCategorieen(): Collection
    {
        return collect(['AGF', 'BB', 'BVH', 'FSKT', 'KV', 'PRW', 'SKCC', 'SSKO', 'ZPE']);
    }

    private function standaardLocaties(): array
    {
        return [
            'Berlicum',
            'Den Bosch',
            'Gemonde',
            'Heeswijk Dinther',
            'Middelrode',
            'Rosmalen',
            'Schijndel',
            'Sint-MichelsGestel',
            'Vught',
        ];
    }
}