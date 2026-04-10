<?php

declare(strict_types=1);

namespace App\Services;

use DomainException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use PDO;
use Throwable;

class VoedselpakketOverzichtService
{
    private const STATUS_NIET_UITGEREIKT = 'NietUitgereikt';
    private const STATUS_UITGEREIKT = 'Uitgereikt';
    private const STATUS_NIET_MEER_INGESCHREVEN = 'NietMeerIngeschreven';

    private function callStoredProcedure(string $procedureName, array $parameters = []): array
    {
        $pdo = DB::connection()->getPdo();

        $placeholders = '';

        if (! empty($parameters)) {
            $placeholders = implode(', ', array_fill(0, count($parameters), '?'));
        }

        $sql = sprintf('CALL %s(%s)', $procedureName, $placeholders);

        $statement = $pdo->prepare($sql);
        $statement->execute($parameters);

        $results = $statement->fetchAll(PDO::FETCH_OBJ);

        while ($statement->nextRowset()) {
            // extra resultsets leegmaken
        }

        $statement->closeCursor();

        return $results;
    }

    public function heeftToegangTotVoedselpakketOverzicht(int $gebruikerId): bool
    {
        try {
            $resultaat = $this->callStoredProcedure('sp_is_voedselpakket_beheerder', [$gebruikerId]);

            if (empty($resultaat)) {
                return false;
            }

            return (int) ($resultaat[0]->HeeftToegang ?? 0) === 1;
        } catch (Throwable $exception) {
            if (! $this->isMissingProcedureException($exception)) {
                throw $exception;
            }

            try {
                $legacyResultaat = $this->callStoredProcedure('sp_is_manager', [$gebruikerId]);

                if (! empty($legacyResultaat) && (int) ($legacyResultaat[0]->IsManager ?? 0) === 1) {
                    return true;
                }
            } catch (Throwable $legacyException) {
                if (! $this->isMissingProcedureException($legacyException)) {
                    throw $legacyException;
                }
            }

            return DB::table('RolPerGebruiker as rpg')
                ->join('Rol as r', 'r.Id', '=', 'rpg.RolId')
                ->where('rpg.GebruikerId', $gebruikerId)
                ->where('rpg.IsActief', 1)
                ->where('r.IsActief', 1)
                ->whereIn('r.Naam', ['Manager', 'Vrijwilliger'])
                ->exists();
        }
    }

    public function getActieveEetwensen(): Collection
    {
        try {
            $eetwensen = $this->callStoredProcedure('sp_get_eetwensen');

            return collect($eetwensen);
        } catch (Throwable $exception) {
            if (! $this->isMissingProcedureException($exception)) {
                throw $exception;
            }

            return DB::table('Eetwens')
                ->select(['Id', 'Naam', 'Omschrijving'])
                ->where('IsActief', 1)
                ->orderBy('Naam')
                ->get();
        }
    }

    public function getGezinnenMetVoedselpakketten(?int $eetwensId = null): Collection
    {
        try {
            $gezinnen = $this->callStoredProcedure('sp_overzicht_voedselpakketten', [$eetwensId]);

            return collect($gezinnen);
        } catch (Throwable $exception) {
            if (! $this->isMissingProcedureException($exception)) {
                throw $exception;
            }

            $query = DB::table('Gezin as g')
                ->join('Voedselpakket as vp', static function ($join): void {
                    $join->on('vp.GezinId', '=', 'g.Id')
                        ->where('vp.IsActief', 1);
                })
                ->leftJoin('Persoon as p', static function ($join): void {
                    $join->on('p.GezinId', '=', 'g.Id')
                        ->where('p.IsVertegenwoordiger', 1)
                        ->where('p.IsActief', 1);
                })
                ->leftJoin('ProductPerVoedselpakket as ppv', static function ($join): void {
                    $join->on('ppv.VoedselpakketId', '=', 'vp.Id')
                        ->where('ppv.IsActief', 1);
                })
                ->selectRaw(
                    "g.Id AS GezinId,
                    g.Naam AS Gezinsnaam,
                    g.Omschrijving,
                    g.AantalVolwassenen,
                    g.AantalKinderen,
                    g.AantalBabys,
                    TRIM(CONCAT(COALESCE(p.Voornaam, ''), ' ', COALESCE(p.Tussenvoegsel, ''), ' ', COALESCE(p.Achternaam, ''))) AS Vertegenwoordiger,
                    COUNT(DISTINCT vp.Id) AS AantalPakketten,
                    COALESCE(SUM(ppv.AantalProductEenheden), 0) AS TotaalProductEenheden"
                )
                ->where('g.IsActief', 1)
                ->when($eetwensId !== null, static function ($subQuery) use ($eetwensId): void {
                    $subQuery->whereExists(static function ($existsQuery) use ($eetwensId): void {
                        $existsQuery->selectRaw('1')
                            ->from('EetwensPerGezin as epg')
                            ->whereColumn('epg.GezinId', 'g.Id')
                            ->where('epg.EetwensId', $eetwensId)
                            ->where('epg.IsActief', 1);
                    });
                })
                ->groupBy(
                    'g.Id',
                    'g.Naam',
                    'g.Omschrijving',
                    'g.AantalVolwassenen',
                    'g.AantalKinderen',
                    'g.AantalBabys',
                    'p.Voornaam',
                    'p.Tussenvoegsel',
                    'p.Achternaam'
                )
                ->orderBy('g.Naam');

            return $query->get();
        }
    }

    public function getGezinDetails(int $gezinId): ?object
    {
        try {
            $resultaat = $this->callStoredProcedure('sp_voedselpakket_gezin_details', [$gezinId]);

            return $resultaat[0] ?? null;
        } catch (Throwable $exception) {
            if (! $this->isMissingProcedureException($exception)) {
                throw $exception;
            }

            return DB::table('Gezin as g')
                ->leftJoin('Persoon as p', static function ($join): void {
                    $join->on('p.GezinId', '=', 'g.Id')
                        ->where('p.IsVertegenwoordiger', 1)
                        ->where('p.IsActief', 1);
                })
                ->leftJoin('EetwensPerGezin as epg', static function ($join): void {
                    $join->on('epg.GezinId', '=', 'g.Id')
                        ->where('epg.IsActief', 1);
                })
                ->leftJoin('Eetwens as e', static function ($join): void {
                    $join->on('e.Id', '=', 'epg.EetwensId')
                        ->where('e.IsActief', 1);
                })
                ->selectRaw(
                    "g.Id AS GezinId,
                    g.Naam AS Gezinsnaam,
                    g.Omschrijving,
                    g.TotaalAantalPersonen,
                    TRIM(CONCAT(COALESCE(p.Voornaam, ''), ' ', COALESCE(p.Tussenvoegsel, ''), ' ', COALESCE(p.Achternaam, ''))) AS Vertegenwoordiger,
                    COALESCE(GROUP_CONCAT(DISTINCT e.Naam ORDER BY e.Naam SEPARATOR ', '), '-') AS Eetwensen"
                )
                ->where('g.Id', $gezinId)
                ->where('g.IsActief', 1)
                ->groupBy(
                    'g.Id',
                    'g.Naam',
                    'g.Omschrijving',
                    'g.TotaalAantalPersonen',
                    'p.Voornaam',
                    'p.Tussenvoegsel',
                    'p.Achternaam'
                )
                ->first();
        }
    }

    public function getVoedselpakkettenVoorGezin(int $gezinId): Collection
    {
        try {
            $resultaat = $this->callStoredProcedure('sp_voedselpakket_overzicht_per_gezin', [$gezinId]);

            return collect($resultaat);
        } catch (Throwable $exception) {
            if (! $this->isMissingProcedureException($exception)) {
                throw $exception;
            }

            return DB::table('Voedselpakket as vp')
                ->leftJoin('ProductPerVoedselpakket as ppv', static function ($join): void {
                    $join->on('ppv.VoedselpakketId', '=', 'vp.Id')
                        ->where('ppv.IsActief', 1);
                })
                ->selectRaw(
                    "vp.Id AS VoedselpakketId,
                    vp.GezinId,
                    vp.PakketNummer,
                    vp.DatumSamenstelling,
                    vp.DatumUitgifte,
                    vp.Status,
                    COALESCE(SUM(ppv.AantalProductEenheden), 0) AS AantalProducten"
                )
                ->where('vp.GezinId', $gezinId)
                ->where('vp.IsActief', 1)
                ->groupBy(
                    'vp.Id',
                    'vp.GezinId',
                    'vp.PakketNummer',
                    'vp.DatumSamenstelling',
                    'vp.DatumUitgifte',
                    'vp.Status'
                )
                ->orderBy('vp.PakketNummer')
                ->get();
        }
    }

    public function getVoedselpakketVoorStatusWijziging(int $voedselpakketId): ?object
    {
        try {
            $resultaat = $this->callStoredProcedure('sp_voedselpakket_status_formulier', [$voedselpakketId]);

            return $resultaat[0] ?? null;
        } catch (Throwable $exception) {
            if (! $this->isMissingProcedureException($exception)) {
                throw $exception;
            }

            return DB::table('Voedselpakket as vp')
                ->join('Gezin as g', 'g.Id', '=', 'vp.GezinId')
                ->selectRaw(
                    "vp.Id AS VoedselpakketId,
                    vp.GezinId,
                    g.Naam AS Gezinsnaam,
                    vp.PakketNummer,
                    vp.Status,
                    vp.DatumUitgifte,
                    CASE WHEN vp.Status = 'NietMeerIngeschreven' THEN 1 ELSE 0 END AS IsNietMeerIngeschreven"
                )
                ->where('vp.Id', $voedselpakketId)
                ->where('vp.IsActief', 1)
                ->where('g.IsActief', 1)
                ->first();
        }
    }

    public function wijzigVoedselpakketStatus(int $voedselpakketId, string $nieuweStatus): void
    {
        try {
            $this->callStoredProcedure('sp_wijzig_voedselpakket_status', [
                $voedselpakketId,
                $nieuweStatus,
            ]);

            return;
        } catch (Throwable $exception) {
            if (! $this->isMissingProcedureException($exception)) {
                throw $exception;
            }
        }

        DB::transaction(function () use ($voedselpakketId, $nieuweStatus): void {
            $huidigPakket = DB::table('Voedselpakket')
                ->select('Id', 'Status', 'IsActief')
                ->where('Id', $voedselpakketId)
                ->lockForUpdate()
                ->first();

            if ($huidigPakket === null || (int) $huidigPakket->IsActief !== 1) {
                throw new DomainException('Het geselecteerde voedselpakket bestaat niet (meer).');
            }

            if ((string) $huidigPakket->Status === self::STATUS_NIET_MEER_INGESCHREVEN) {
                throw new DomainException(
                    'Dit gezin is niet meer ingeschreven bij de voedselbank en daarom kan er geen voedselpakket worden uitgereikt'
                );
            }

            if (! in_array($nieuweStatus, [self::STATUS_NIET_UITGEREIKT, self::STATUS_UITGEREIKT], true)) {
                throw new DomainException('De geselecteerde status is ongeldig.');
            }

            $datumUitgifte = $nieuweStatus === self::STATUS_UITGEREIKT
                ? now()->toDateString()
                : null;

            DB::table('Voedselpakket')
                ->where('Id', $voedselpakketId)
                ->update([
                    'Status' => $nieuweStatus,
                    'DatumUitgifte' => $datumUitgifte,
                ]);
        });
    }

    public function getStatusNietMeerIngeschreven(): string
    {
        return self::STATUS_NIET_MEER_INGESCHREVEN;
    }

    public function getStatusUitgereikt(): string
    {
        return self::STATUS_UITGEREIKT;
    }

    public function getStatusNietUitgereikt(): string
    {
        return self::STATUS_NIET_UITGEREIKT;
    }

    private function isMissingProcedureException(Throwable $exception): bool
    {
        return str_contains(
            strtolower($exception->getMessage()),
            'procedure'
        ) && str_contains(strtolower($exception->getMessage()), 'does not exist');
    }
}
