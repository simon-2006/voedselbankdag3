<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Maak alle stored procedures aan die nodig zijn voor het allergie-overzicht
     * en het wijzigen van allergie-informatie.
     */
    public function up(): void
    {
        if (config('database.default') !== 'mysql') {
            return;
        }

        DB::unprepared('DROP PROCEDURE IF EXISTS sp_allergie_overzicht_allergieen');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_allergie_overzicht_gezinnen');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_allergie_gezin_samenvatting');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_allergie_gezin_personen');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_allergie_persoon_huidige_allergie');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_allergie_wijzig_persoon');

        DB::unprepared(<<<'SQL'
            CREATE PROCEDURE sp_allergie_overzicht_allergieen()
            BEGIN
                SELECT a.Id, a.Naam
                FROM Allergie a
                WHERE a.IsActief = b'1'
                ORDER BY a.Naam;
            END
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE PROCEDURE sp_allergie_overzicht_gezinnen(IN p_allergie_id BIGINT)
            BEGIN
                SELECT
                    g.Id,
                    g.Naam,
                    g.Omschrijving,
                    g.AantalVolwassenen,
                    g.AantalKinderen,
                    g.AantalBabys,
                    COALESCE(
                        (
                            SELECT CONCAT_WS(' ', vp.Voornaam, NULLIF(vp.Tussenvoegsel, ''), vp.Achternaam)
                            FROM Persoon vp
                            WHERE vp.GezinId = g.Id
                              AND vp.IsActief = b'1'
                              AND vp.IsVertegenwoordiger = b'1'
                            LIMIT 1
                        ),
                        '-'
                    ) AS Vertegenwoordiger
                FROM Gezin g
                WHERE g.IsActief = b'1'
                  AND EXISTS (
                    SELECT 1
                    FROM Persoon p
                    INNER JOIN AllergiePerPersoon ap
                        ON ap.PersoonId = p.Id
                       AND ap.IsActief = b'1'
                    INNER JOIN Allergie a
                        ON a.Id = ap.AllergieId
                       AND a.IsActief = b'1'
                    WHERE p.GezinId = g.Id
                      AND p.IsActief = b'1'
                      AND (p_allergie_id IS NULL OR a.Id = p_allergie_id)
                  )
                ORDER BY g.Naam;
            END
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE PROCEDURE sp_allergie_gezin_samenvatting(IN p_gezin_id BIGINT)
            BEGIN
                SELECT
                    g.Id,
                    g.Naam,
                    g.Omschrijving,
                    g.TotaalAantalPersonen
                FROM Gezin g
                WHERE g.Id = p_gezin_id
                  AND g.IsActief = b'1'
                LIMIT 1;
            END
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE PROCEDURE sp_allergie_gezin_personen(IN p_gezin_id BIGINT)
            BEGIN
                SELECT
                    p.Id AS PersoonId,
                    CONCAT_WS(' ', p.Voornaam, NULLIF(p.Tussenvoegsel, ''), p.Achternaam) AS Naam,
                    p.TypePersoon,
                    CASE
                        WHEN p.IsVertegenwoordiger = b'1' THEN 'Vertegenwoordiger'
                        ELSE 'Gezinslid'
                    END AS Gezinsrol,
                    COALESCE(
                        GROUP_CONCAT(DISTINCT a.Naam ORDER BY a.Naam SEPARATOR ', '),
                        '-'
                    ) AS Allergie
                FROM Persoon p
                LEFT JOIN AllergiePerPersoon ap
                    ON ap.PersoonId = p.Id
                   AND ap.IsActief = b'1'
                LEFT JOIN Allergie a
                    ON a.Id = ap.AllergieId
                   AND a.IsActief = b'1'
                WHERE p.GezinId = p_gezin_id
                  AND p.IsActief = b'1'
                GROUP BY p.Id, p.Voornaam, p.Tussenvoegsel, p.Achternaam, p.TypePersoon, p.IsVertegenwoordiger
                ORDER BY p.Id;
            END
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE PROCEDURE sp_allergie_persoon_huidige_allergie(IN p_persoon_id BIGINT)
            BEGIN
                SELECT
                    p.Id AS PersoonId,
                    p.GezinId,
                    CONCAT_WS(' ', p.Voornaam, NULLIF(p.Tussenvoegsel, ''), p.Achternaam) AS Naam,
                    ap.AllergieId,
                    a.Naam AS AllergieNaam,
                    a.AnafylactischRisico
                FROM Persoon p
                LEFT JOIN AllergiePerPersoon ap
                    ON ap.PersoonId = p.Id
                   AND ap.IsActief = b'1'
                LEFT JOIN Allergie a
                    ON a.Id = ap.AllergieId
                   AND a.IsActief = b'1'
                WHERE p.Id = p_persoon_id
                  AND p.IsActief = b'1'
                ORDER BY ap.Id
                LIMIT 1;
            END
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE PROCEDURE sp_allergie_wijzig_persoon(
                IN p_persoon_id BIGINT,
                IN p_oude_allergie_id BIGINT,
                IN p_nieuwe_allergie_id BIGINT
            )
            BEGIN
                IF p_oude_allergie_id IS NULL THEN
                    INSERT INTO AllergiePerPersoon (
                        PersoonId,
                        AllergieId,
                        IsActief,
                        DatumAangemaakt,
                        DatumGewijzigd
                    )
                    SELECT
                        p_persoon_id,
                        p_nieuwe_allergie_id,
                        b'1',
                        NOW(6),
                        NOW(6)
                    FROM DUAL
                    WHERE NOT EXISTS (
                        SELECT 1
                        FROM AllergiePerPersoon ap
                        WHERE ap.PersoonId = p_persoon_id
                          AND ap.AllergieId = p_nieuwe_allergie_id
                    );
                ELSE
                    UPDATE AllergiePerPersoon ap
                    SET ap.AllergieId = p_nieuwe_allergie_id,
                        ap.DatumGewijzigd = NOW(6)
                    WHERE ap.PersoonId = p_persoon_id
                      AND ap.AllergieId = p_oude_allergie_id
                    LIMIT 1;

                    IF ROW_COUNT() = 0 THEN
                        INSERT INTO AllergiePerPersoon (
                            PersoonId,
                            AllergieId,
                            IsActief,
                            DatumAangemaakt,
                            DatumGewijzigd
                        )
                        SELECT
                            p_persoon_id,
                            p_nieuwe_allergie_id,
                            b'1',
                            NOW(6),
                            NOW(6)
                        FROM DUAL
                        WHERE NOT EXISTS (
                            SELECT 1
                            FROM AllergiePerPersoon ap
                            WHERE ap.PersoonId = p_persoon_id
                              AND ap.AllergieId = p_nieuwe_allergie_id
                        );
                    END IF;
                END IF;
            END
        SQL);
    }

    /**
     * Ruim alle stored procedures op als de migration wordt teruggedraaid.
     */
    public function down(): void
    {
        if (config('database.default') !== 'mysql') {
            return;
        }

        DB::unprepared('DROP PROCEDURE IF EXISTS sp_allergie_wijzig_persoon');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_allergie_persoon_huidige_allergie');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_allergie_gezin_personen');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_allergie_gezin_samenvatting');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_allergie_overzicht_gezinnen');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_allergie_overzicht_allergieen');
    }
};
