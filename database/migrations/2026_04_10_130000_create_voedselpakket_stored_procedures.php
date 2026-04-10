<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (config('database.default') !== 'mysql') {
            return;
        }

        DB::unprepared('DROP PROCEDURE IF EXISTS sp_is_manager');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_overzicht_voedselpakketten');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_is_manager_legacy');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_overzicht_voedselpakketten_legacy');

        if (
            Schema::hasTable('Rol')
            && Schema::hasTable('RolPerGebruiker')
            && Schema::hasTable('Gezin')
            && Schema::hasTable('Voedselpakket')
            && Schema::hasTable('EetwensPerGezin')
        ) {
            DB::unprepared(<<<'SQL'
                CREATE PROCEDURE sp_is_manager(IN p_gebruiker_id BIGINT UNSIGNED)
                BEGIN
                    SELECT
                        CASE
                            WHEN EXISTS (
                                SELECT 1
                                FROM RolPerGebruiker rpg
                                INNER JOIN Rol r ON r.Id = rpg.RolId
                                WHERE rpg.GebruikerId = p_gebruiker_id
                                  AND rpg.IsActief = b'1'
                                  AND r.IsActief = b'1'
                                  AND r.Naam = 'Manager'
                            ) THEN 1
                            ELSE 0
                        END AS IsManager;
                END
            SQL);

            DB::unprepared(<<<'SQL'
                CREATE PROCEDURE sp_overzicht_voedselpakketten(IN p_eetwens_id BIGINT UNSIGNED)
                BEGIN
                    SELECT
                        g.Id AS GezinId,
                        g.Naam AS Gezinsnaam,
                        g.Omschrijving,
                        g.AantalVolwassenen,
                        g.AantalKinderen,
                        g.AantalBabys,
                        TRIM(CONCAT(COALESCE(p.Voornaam, ''), ' ', COALESCE(p.Tussenvoegsel, ''), ' ', COALESCE(p.Achternaam, ''))) AS Vertegenwoordiger,
                        COUNT(DISTINCT vp.Id) AS AantalPakketten,
                        COALESCE(SUM(ppv.AantalProductEenheden), 0) AS TotaalProductEenheden
                    FROM Voedselpakket vp
                    INNER JOIN Gezin g ON g.Id = vp.GezinId
                    LEFT JOIN Persoon p
                        ON p.GezinId = g.Id
                       AND p.IsVertegenwoordiger = b'1'
                       AND p.IsActief = b'1'
                    LEFT JOIN ProductPerVoedselpakket ppv ON ppv.VoedselpakketId = vp.Id
                    WHERE g.IsActief = b'1'
                      AND vp.IsActief = b'1'
                      AND (
                          p_eetwens_id IS NULL
                          OR EXISTS (
                              SELECT 1
                              FROM EetwensPerGezin epg
                              WHERE epg.GezinId = g.Id
                                AND epg.EetwensId = p_eetwens_id
                                AND epg.IsActief = b'1'
                          )
                      )
                    GROUP BY
                        g.Id,
                        g.Naam,
                        g.Omschrijving,
                        g.AantalVolwassenen,
                        g.AantalKinderen,
                        g.AantalBabys,
                        p.Voornaam,
                        p.Tussenvoegsel,
                        p.Achternaam
                    ORDER BY g.Naam;
                END
            SQL);
        }

        if (
            Schema::hasTable('gebruiker')
            && Schema::hasTable('persoon')
            && Schema::hasTable('klanten')
            && Schema::hasTable('voedselpakketten')
            && Schema::hasTable('wens_allergies')
            && Schema::hasTable('klant_wens')
        ) {
            DB::unprepared(<<<'SQL'
                CREATE PROCEDURE sp_is_manager_legacy(IN p_gebruiker_id BIGINT UNSIGNED)
                BEGIN
                    SELECT
                        CASE
                            WHEN EXISTS (
                                SELECT 1
                                FROM gebruiker g
                                INNER JOIN persoon p ON p.Id = g.PersoonId
                                WHERE g.Id = p_gebruiker_id
                                  AND g.IsActief = 1
                                  AND p.IsActief = 1
                                  AND LOWER(p.TypePersoon) = 'manager'
                            ) THEN 1
                            ELSE 0
                        END AS IsManager;
                END
            SQL);

            DB::unprepared(<<<'SQL'
                CREATE PROCEDURE sp_overzicht_voedselpakketten_legacy(IN p_eetwens_id BIGINT UNSIGNED)
                BEGIN
                    SELECT
                        k.id AS GezinId,
                        k.gezinsnaam AS Gezinsnaam,
                        k.adres AS Omschrijving,
                        k.aantal_volwassenen AS AantalVolwassenen,
                        k.aantal_kinderen AS AantalKinderen,
                        k.aantal_babys AS AantalBabys,
                        '' AS Vertegenwoordiger,
                        COUNT(DISTINCT vp.id) AS AantalPakketten,
                        COALESCE(SUM(pp.aantal), 0) AS TotaalProductEenheden
                    FROM voedselpakketten vp
                    INNER JOIN klanten k ON k.id = vp.klant_id
                    LEFT JOIN pakket_product pp ON pp.pakket_id = vp.id
                    WHERE (
                        p_eetwens_id IS NULL
                        OR EXISTS (
                            SELECT 1
                            FROM klant_wens kw
                            WHERE kw.klant_id = k.id
                              AND kw.wens_id = p_eetwens_id
                        )
                    )
                    GROUP BY
                        k.id,
                        k.gezinsnaam,
                        k.adres,
                        k.aantal_volwassenen,
                        k.aantal_kinderen,
                        k.aantal_babys
                    ORDER BY k.gezinsnaam;
                END
            SQL);
        }
    }

    public function down(): void
    {
        if (config('database.default') !== 'mysql') {
            return;
        }

        DB::unprepared('DROP PROCEDURE IF EXISTS sp_is_manager');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_overzicht_voedselpakketten');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_is_manager_legacy');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_overzicht_voedselpakketten_legacy');
    }
};
