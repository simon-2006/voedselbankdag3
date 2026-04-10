-- =============================================
-- VOEDSELBANK MAASKANTJE - STORED PROCEDURES
-- Domein: Allergie-overzicht en allergie wijzigen
-- =============================================

USE voedselbank_maaskantje_dag_na_dag_2;

DELIMITER $$

-- -----------------------------------------------------
-- Procedure: sp_allergie_overzicht_allergieen
-- Doel: Geeft alle actieve allergieën voor de filter-dropdown.
-- -----------------------------------------------------
DROP PROCEDURE IF EXISTS sp_allergie_overzicht_allergieen $$
CREATE PROCEDURE sp_allergie_overzicht_allergieen()
BEGIN
    SELECT a.Id, a.Naam
    FROM Allergie a
    WHERE a.IsActief = b'1'
    ORDER BY a.Naam;
END $$

-- -----------------------------------------------------
-- Procedure: sp_allergie_overzicht_gezinnen
-- Doel: Overzicht van gezinnen met allergieën, optioneel gefilterd
--       op 1 geselecteerde allergie.
-- Input:
--   p_allergie_id = NULL -> alle gezinnen met minimaal 1 allergie
--   p_allergie_id = id   -> alleen gezinnen met die allergie
-- -----------------------------------------------------
DROP PROCEDURE IF EXISTS sp_allergie_overzicht_gezinnen $$
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
END $$

-- -----------------------------------------------------
-- Procedure: sp_allergie_gezin_samenvatting
-- Doel: Basisinformatie van 1 gezin tonen op de detailpagina.
-- -----------------------------------------------------
DROP PROCEDURE IF EXISTS sp_allergie_gezin_samenvatting $$
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
END $$

-- -----------------------------------------------------
-- Procedure: sp_allergie_gezin_personen
-- Doel: Alle gezinsleden met rol en allergieën tonen.
-- -----------------------------------------------------
DROP PROCEDURE IF EXISTS sp_allergie_gezin_personen $$
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
END $$

-- -----------------------------------------------------
-- Procedure: sp_allergie_persoon_huidige_allergie
-- Doel: Huidige allergie en risiconiveau van 1 persoon ophalen.
-- -----------------------------------------------------
DROP PROCEDURE IF EXISTS sp_allergie_persoon_huidige_allergie $$
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
END $$

-- -----------------------------------------------------
-- Procedure: sp_allergie_wijzig_persoon
-- Doel: Allergie van persoon wijzigen of toevoegen als er nog geen
--       actieve koppeling bestaat.
-- -----------------------------------------------------
DROP PROCEDURE IF EXISTS sp_allergie_wijzig_persoon $$
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
END $$

DELIMITER ;
