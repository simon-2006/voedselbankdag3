<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use PDO;

class VoedselpakketOverzichtService
{
    private function callStoredProcedure(string $procedureName, array $parameters = []): array
    {
        $pdo = DB::connection()->getPdo();

        $placeholders = '';

        if (!empty($parameters)) {
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

    public function isManager(int $gebruikerId): bool
    {
        $resultaat = $this->callStoredProcedure('sp_is_manager', [$gebruikerId]);

        if (empty($resultaat)) {
            return false;
        }

        return (int) ($resultaat[0]->IsManager ?? 0) === 1;
    }

    public function getActieveEetwensen(): Collection
    {
        $eetwensen = $this->callStoredProcedure('sp_get_eetwensen');

        return collect($eetwensen);
    }

    public function getGezinnenMetVoedselpakketten(?int $eetwensId = null): Collection
    {
        $gezinnen = $this->callStoredProcedure('sp_overzicht_voedselpakketten', [$eetwensId]);

        return collect($gezinnen);
    }
}
