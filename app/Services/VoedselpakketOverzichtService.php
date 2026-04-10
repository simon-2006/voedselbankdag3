<?php

namespace App\Services;

use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class VoedselpakketOverzichtService
{
    /**
     * Haalt alle actieve eetwensen op voor de dropdown.
     */
    public function getActieveEetwensen(): Collection
    {
        if ($this->hasDag3Schema()) {
            return DB::table('Eetwens')
                ->where('IsActief', 1)
                ->orderBy('Naam')
                ->get(['Id', 'Naam']);
        }

        if ($this->hasLegacySchema()) {
            return DB::table('wens_allergies')
                ->orderBy('beschrijving')
                ->get([
                    'id as Id',
                    DB::raw('beschrijving as Naam'),
                ]);
        }

        return collect();
    }

    /**
     * Controleert of de ingelogde gebruiker manager is.
     * Probeert eerst de stored procedure, daarna fallback naar joins.
     */
    public function isManager(int $gebruikerId): bool
    {
        if (Schema::hasTable('Rol') && Schema::hasTable('RolPerGebruiker')) {
            try {
                $resultaat = DB::select('CALL sp_is_manager(?)', [$gebruikerId]);

                return isset($resultaat[0]) && (int) ($resultaat[0]->IsManager ?? 0) === 1;
            } catch (QueryException $exception) {
                if (! $this->isStoredProcedureMissing($exception)) {
                    throw $exception;
                }

                Log::warning('Stored procedure sp_is_manager ontbreekt, fallback query gebruikt.', [
                    'gebruiker_id' => $gebruikerId,
                    'error' => $exception->getMessage(),
                ]);
            }

            return DB::table('RolPerGebruiker as rpg')
                ->join('Rol as r', 'r.Id', '=', 'rpg.RolId')
                ->where('rpg.GebruikerId', $gebruikerId)
                ->where('r.Naam', 'Manager')
                ->where('rpg.IsActief', 1)
                ->where('r.IsActief', 1)
                ->exists();
        }

        if (Schema::hasTable('persoon') && Schema::hasTable('gebruiker')) {
            return DB::table('gebruiker as g')
                ->join('persoon as p', 'p.Id', '=', 'g.PersoonId')
                ->where('g.Id', $gebruikerId)
                ->where('g.IsActief', 1)
                ->where('p.IsActief', 1)
                ->whereRaw('LOWER(p.TypePersoon) = ?', ['manager'])
                ->exists();
        }

        return false;
    }

    /**
     * Overzicht van gezinnen met voedselpakketten.
     * Probeert eerst stored procedure, daarna fallback query met joins.
     */
    public function getGezinnenMetVoedselpakketten(?int $eetwensId): Collection
    {
        if ($this->hasDag3Schema()) {
            try {
                $rows = DB::select('CALL sp_overzicht_voedselpakketten(?)', [$eetwensId]);

                return collect($rows);
            } catch (QueryException $exception) {
                if (! $this->isStoredProcedureMissing($exception)) {
                    throw $exception;
                }

                Log::warning('Stored procedure sp_overzicht_voedselpakketten ontbreekt, fallback query gebruikt.', [
                    'eetwens_id' => $eetwensId,
                    'error' => $exception->getMessage(),
                ]);
            }

            $query = DB::table('Voedselpakket as vp')
                ->join('Gezin as g', 'g.Id', '=', 'vp.GezinId')
                ->leftJoin('Persoon as p', function ($join) {
                    $join->on('p.GezinId', '=', 'g.Id')
                        ->where('p.IsVertegenwoordiger', 1)
                        ->where('p.IsActief', 1);
                })
                ->leftJoin('ProductPerVoedselpakket as ppv', 'ppv.VoedselpakketId', '=', 'vp.Id')
                ->where('g.IsActief', 1)
                ->where('vp.IsActief', 1)
                ->select([
                    'g.Id as GezinId',
                    'g.Naam as Gezinsnaam',
                    'g.Omschrijving',
                    'g.AantalVolwassenen',
                    'g.AantalKinderen',
                    'g.AantalBabys',
                    DB::raw("TRIM(CONCAT(COALESCE(p.Voornaam, ''), ' ', COALESCE(p.Tussenvoegsel, ''), ' ', COALESCE(p.Achternaam, ''))) as Vertegenwoordiger"),
                    DB::raw('COUNT(DISTINCT vp.Id) as AantalPakketten'),
                    DB::raw('COALESCE(SUM(ppv.AantalProductEenheden), 0) as TotaalProductEenheden'),
                ])
                ->groupBy([
                    'g.Id',
                    'g.Naam',
                    'g.Omschrijving',
                    'g.AantalVolwassenen',
                    'g.AantalKinderen',
                    'g.AantalBabys',
                    'p.Voornaam',
                    'p.Tussenvoegsel',
                    'p.Achternaam',
                ])
                ->orderBy('g.Naam');

            if ($eetwensId !== null) {
                $query->whereExists(function ($existsQuery) use ($eetwensId) {
                    $existsQuery->select(DB::raw(1))
                        ->from('EetwensPerGezin as epg')
                        ->whereColumn('epg.GezinId', 'g.Id')
                        ->where('epg.EetwensId', $eetwensId)
                        ->where('epg.IsActief', 1);
                });
            }

            return $query->get();
        }

        if ($this->hasLegacySchema()) {
            $query = DB::table('voedselpakketten as vp')
                ->join('klanten as k', 'k.id', '=', 'vp.klant_id')
                ->leftJoin('pakket_product as pp', 'pp.pakket_id', '=', 'vp.id')
                ->select([
                    'k.id as GezinId',
                    'k.gezinsnaam as Gezinsnaam',
                    DB::raw('k.adres as Omschrijving'),
                    'k.aantal_volwassenen as AantalVolwassenen',
                    'k.aantal_kinderen as AantalKinderen',
                    'k.aantal_babys as AantalBabys',
                    DB::raw("'' as Vertegenwoordiger"),
                    DB::raw('COUNT(DISTINCT vp.id) as AantalPakketten'),
                    DB::raw('COALESCE(SUM(pp.aantal), 0) as TotaalProductEenheden'),
                ])
                ->groupBy([
                    'k.id',
                    'k.gezinsnaam',
                    'k.adres',
                    'k.aantal_volwassenen',
                    'k.aantal_kinderen',
                    'k.aantal_babys',
                ])
                ->orderBy('k.gezinsnaam');

            if ($eetwensId !== null) {
                $query->whereExists(function ($existsQuery) use ($eetwensId) {
                    $existsQuery->select(DB::raw(1))
                        ->from('klant_wens as kw')
                        ->whereColumn('kw.klant_id', 'k.id')
                        ->where('kw.wens_id', $eetwensId);
                });
            }

            return $query->get();
        }

        throw new \RuntimeException(
            'Benodigde tabellen ontbreken. Importeer eerst database/voedselbank_dag3.sql.'
        );
    }

    private function hasDag3Schema(): bool
    {
        $requiredTables = [
            'Gezin',
            'Persoon',
            'EetwensPerGezin',
            'Eetwens',
            'Voedselpakket',
            'ProductPerVoedselpakket',
        ];

        foreach ($requiredTables as $table) {
            if (! Schema::hasTable($table)) {
                return false;
            }
        }

        return true;
    }

    private function hasLegacySchema(): bool
    {
        $requiredTables = [
            'gebruiker',
            'persoon',
            'klanten',
            'voedselpakketten',
            'wens_allergies',
            'klant_wens',
            'pakket_product',
        ];

        foreach ($requiredTables as $table) {
            if (! Schema::hasTable($table)) {
                return false;
            }
        }

        return true;
    }

    private function isStoredProcedureMissing(QueryException $exception): bool
    {
        $message = strtolower($exception->getMessage());

        return str_contains($message, 'procedure') && str_contains($message, 'does not exist');
    }
}
