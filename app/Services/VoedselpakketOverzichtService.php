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
     * Tabellen die volgens User Story 03 gebruikt moeten worden.
     */
    private const USER_STORY_TABLES = [
        'Gezin',
        'Persoon',
        'EetwensPerGezin',
        'Eetwens',
        'Voedselpakket',
        'ProductPerVoedselpakket',
    ];

    public function getActieveEetwensen(): Collection
    {
        $this->assertUserStoryTablesPresent();

        $eetwensen = DB::table('Eetwens')
            ->where('IsActief', 1)
            ->orderBy('Naam')
            ->get(['Id', 'Naam']);

        Log::channel('voedselpakket')->info('Actieve eetwensen geladen.', [
            'aantal_eetwensen' => $eetwensen->count(),
        ]);

        return $eetwensen;
    }

    public function isManager(int $gebruikerId): bool
    {
        Log::channel('voedselpakket')->debug('Manager-check gestart.', [
            'gebruiker_id' => $gebruikerId,
        ]);

        if (Schema::hasTable('Rol') && Schema::hasTable('RolPerGebruiker')) {
            try {
                $resultaat = DB::select('CALL sp_is_manager(?)', [$gebruikerId]);
                $isManager = isset($resultaat[0]) && (int) ($resultaat[0]->IsManager ?? 0) === 1;

                Log::channel('voedselpakket')->info('Manager-check via stored procedure uitgevoerd.', [
                    'gebruiker_id' => $gebruikerId,
                    'is_manager' => $isManager,
                ]);

                return $isManager;
            } catch (QueryException $exception) {
                if (! $this->isStoredProcedureMissing($exception)) {
                    throw $exception;
                }

                Log::channel('voedselpakket')->warning('Stored procedure sp_is_manager ontbreekt, join-fallback gebruikt.', [
                    'gebruiker_id' => $gebruikerId,
                    'error' => $exception->getMessage(),
                ]);
            }

            $isManager = DB::table('RolPerGebruiker as rpg')
                ->join('Rol as r', 'r.Id', '=', 'rpg.RolId')
                ->where('rpg.GebruikerId', $gebruikerId)
                ->where('r.Naam', 'Manager')
                ->where('rpg.IsActief', 1)
                ->where('r.IsActief', 1)
                ->exists();

            Log::channel('voedselpakket')->info('Manager-check via join-fallback uitgevoerd.', [
                'gebruiker_id' => $gebruikerId,
                'is_manager' => $isManager,
            ]);

            return $isManager;
        }

        if (Schema::hasTable('Gebruiker') && Schema::hasTable('Persoon')) {
            $isManager = DB::table('Gebruiker as g')
                ->join('Persoon as p', 'p.Id', '=', 'g.PersoonId')
                ->where('g.Id', $gebruikerId)
                ->where('g.IsActief', 1)
                ->where('p.IsActief', 1)
                ->whereRaw('LOWER(p.TypePersoon) = ?', ['manager'])
                ->exists();

            Log::channel('voedselpakket')->info('Manager-check via Persoon.TypePersoon fallback uitgevoerd.', [
                'gebruiker_id' => $gebruikerId,
                'is_manager' => $isManager,
            ]);

            return $isManager;
        }

        Log::channel('voedselpakket')->warning('Manager-check mislukt: tabellen voor autorisatie ontbreken.', [
            'gebruiker_id' => $gebruikerId,
        ]);

        return false;
    }

    public function getGezinnenMetVoedselpakketten(?int $eetwensId): Collection
    {
        $this->assertUserStoryTablesPresent();

        Log::channel('voedselpakket')->debug('Ophalen overzicht voedselpakketten gestart.', [
            'eetwens_id' => $eetwensId,
        ]);

        try {
            $rows = DB::select('CALL sp_overzicht_voedselpakketten(?)', [$eetwensId]);
            $resultaat = collect($rows);

            Log::channel('voedselpakket')->info('Overzicht geladen via stored procedure.', [
                'eetwens_id' => $eetwensId,
                'aantal_gezinnen' => $resultaat->count(),
            ]);

            return $resultaat;
        } catch (QueryException $exception) {
            if (! $this->isStoredProcedureMissing($exception)) {
                throw $exception;
            }

            Log::channel('voedselpakket')->warning('Stored procedure sp_overzicht_voedselpakketten ontbreekt, join-fallback gebruikt.', [
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

        $resultaat = $query->get();

        Log::channel('voedselpakket')->info('Overzicht geladen via join-fallback.', [
            'eetwens_id' => $eetwensId,
            'aantal_gezinnen' => $resultaat->count(),
        ]);

        return $resultaat;
    }

    private function assertUserStoryTablesPresent(): void
    {
        foreach (self::USER_STORY_TABLES as $table) {
            if (! Schema::hasTable($table)) {
                throw new \RuntimeException(
                    'User Story 03 tabellen ontbreken. Importeer database/voedselbank_dag3.sql en gebruik die database in .env.'
                );
            }
        }
    }

    private function isStoredProcedureMissing(QueryException $exception): bool
    {
        $message = strtolower($exception->getMessage());

        return str_contains($message, 'procedure') && str_contains($message, 'does not exist');
    }
}
