USE Voedselbank_Maaskantje_dag_na_dag_2;

-- ============================================================
-- VOEDSELPAKKETTEN - USER STORY 03 + 04
-- Gebruikte tabellen:
-- Gezin, Persoon, EetwensPerGezin, Eetwens, Voedselpakket, ProductPerVoedselPakket
-- ============================================================

DROP PROCEDURE IF EXISTS sp_is_voedselpakket_beheerder;
DROP PROCEDURE IF EXISTS sp_get_eetwensen;
DROP PROCEDURE IF EXISTS sp_overzicht_voedselpakketten;
DROP PROCEDURE IF EXISTS sp_voedselpakket_gezin_details;
DROP PROCEDURE IF EXISTS sp_voedselpakket_overzicht_per_gezin;
DROP PROCEDURE IF EXISTS sp_voedselpakket_status_formulier;
DROP PROCEDURE IF EXISTS sp_wijzig_voedselpakket_status;

DELIMITER //

-- ------------------------------------------------------------
-- Controle: heeft gebruiker toegang (Manager of Vrijwilliger)
-- ------------------------------------------------------------
CREATE PROCEDURE sp_is_voedselpakket_beheerder(IN p_gebruiker_id BIGINT UNSIGNED)
BEGIN
    SELECT
        CASE
            WHEN EXISTS (
                SELECT 1
                FROM RolPerGebruiker AS rpg
                INNER JOIN Rol AS r
                    ON r.Id = rpg.RolId
                WHERE rpg.GebruikerId = p_gebruiker_id
                  AND rpg.IsActief = b'1'
                  AND r.IsActief = b'1'
                  AND r.Naam IN ('Manager', 'Vrijwilliger')
            ) THEN 1
            ELSE 0
        END AS HeeftToegang;
END //

-- ------------------------------------------------------------
-- Filteropties eetwensen
-- ------------------------------------------------------------
CREATE PROCEDURE sp_get_eetwensen()
BEGIN
    SELECT
        e.Id,
        e.Naam,
        e.Omschrijving
    FROM Eetwens AS e
    WHERE e.IsActief = b'1'
    ORDER BY e.Naam ASC;
END //

-- ------------------------------------------------------------
-- Overzicht gezinnen met voedselpakketten (optioneel per eetwens)
-- ------------------------------------------------------------
CREATE PROCEDURE sp_overzicht_voedselpakketten(IN p_eetwens_id BIGINT UNSIGNED)
BEGIN
    SELECT
        g.Id AS GezinId,
        g.Naam AS Gezinsnaam,
        g.Omschrijving,
        g.AantalVolwassenen,
        g.AantalKinderen,
        g.AantalBabys,
        TRIM(
            CONCAT(
                COALESCE(p.Voornaam, ''),
                ' ',
                COALESCE(p.Tussenvoegsel, ''),
                ' ',
                COALESCE(p.Achternaam, '')
            )
        ) AS Vertegenwoordiger,
        COUNT(DISTINCT vp.Id) AS AantalPakketten,
        COALESCE(SUM(ppv.AantalProductEenheden), 0) AS TotaalProductEenheden
    FROM Gezin AS g
    INNER JOIN Voedselpakket AS vp
        ON vp.GezinId = g.Id
       AND vp.IsActief = b'1'
    LEFT JOIN Persoon AS p
        ON p.GezinId = g.Id
       AND p.IsVertegenwoordiger = b'1'
       AND p.IsActief = b'1'
    LEFT JOIN ProductPerVoedselpakket AS ppv
        ON ppv.VoedselpakketId = vp.Id
       AND ppv.IsActief = b'1'
    WHERE g.IsActief = b'1'
      AND (
            p_eetwens_id IS NULL
            OR EXISTS (
                SELECT 1
                FROM EetwensPerGezin AS epg
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
    ORDER BY g.Naam ASC;
END //

-- ------------------------------------------------------------
-- Gegevens van 1 gezin op de detailpagina
-- ------------------------------------------------------------
CREATE PROCEDURE sp_voedselpakket_gezin_details(IN p_gezin_id BIGINT UNSIGNED)
BEGIN
    SELECT
        g.Id AS GezinId,
        g.Naam AS Gezinsnaam,
        g.Omschrijving,
        g.TotaalAantalPersonen,
        TRIM(
            CONCAT(
                COALESCE(p.Voornaam, ''),
                ' ',
                COALESCE(p.Tussenvoegsel, ''),
                ' ',
                COALESCE(p.Achternaam, '')
            )
        ) AS Vertegenwoordiger,
        COALESCE(
            GROUP_CONCAT(DISTINCT e.Naam ORDER BY e.Naam SEPARATOR ', '),
            '-'
        ) AS Eetwensen
    FROM Gezin AS g
    LEFT JOIN Persoon AS p
        ON p.GezinId = g.Id
       AND p.IsVertegenwoordiger = b'1'
       AND p.IsActief = b'1'
    LEFT JOIN EetwensPerGezin AS epg
        ON epg.GezinId = g.Id
       AND epg.IsActief = b'1'
    LEFT JOIN Eetwens AS e
        ON e.Id = epg.EetwensId
       AND e.IsActief = b'1'
    WHERE g.Id = p_gezin_id
      AND g.IsActief = b'1'
    GROUP BY
        g.Id,
        g.Naam,
        g.Omschrijving,
        g.TotaalAantalPersonen,
        p.Voornaam,
        p.Tussenvoegsel,
        p.Achternaam;
END //

-- ------------------------------------------------------------
-- Alle voedselpakketten van 1 gezin
-- ------------------------------------------------------------
CREATE PROCEDURE sp_voedselpakket_overzicht_per_gezin(IN p_gezin_id BIGINT UNSIGNED)
BEGIN
    SELECT
        vp.Id AS VoedselpakketId,
        vp.GezinId,
        vp.PakketNummer,
        vp.DatumSamenstelling,
        vp.DatumUitgifte,
        vp.Status,
        COALESCE(SUM(ppv.AantalProductEenheden), 0) AS AantalProducten
    FROM Voedselpakket AS vp
    LEFT JOIN ProductPerVoedselpakket AS ppv
        ON ppv.VoedselpakketId = vp.Id
       AND ppv.IsActief = b'1'
    WHERE vp.GezinId = p_gezin_id
      AND vp.IsActief = b'1'
    GROUP BY
        vp.Id,
        vp.GezinId,
        vp.PakketNummer,
        vp.DatumSamenstelling,
        vp.DatumUitgifte,
        vp.Status
    ORDER BY vp.PakketNummer ASC;
END //

-- ------------------------------------------------------------
-- Gegevens voor statusformulier van 1 voedselpakket
-- ------------------------------------------------------------
CREATE PROCEDURE sp_voedselpakket_status_formulier(IN p_voedselpakket_id BIGINT UNSIGNED)
BEGIN
    SELECT
        vp.Id AS VoedselpakketId,
        vp.GezinId,
        g.Naam AS Gezinsnaam,
        vp.PakketNummer,
        vp.Status,
        vp.DatumUitgifte,
        CASE
            WHEN vp.Status = 'NietMeerIngeschreven' THEN 1
            ELSE 0
        END AS IsNietMeerIngeschreven
    FROM Voedselpakket AS vp
    INNER JOIN Gezin AS g
        ON g.Id = vp.GezinId
       AND g.IsActief = b'1'
    WHERE vp.Id = p_voedselpakket_id
      AND vp.IsActief = b'1'
    LIMIT 1;
END //

-- ------------------------------------------------------------
-- Wijzig status voedselpakket (User Story 04)
-- Regels:
-- 1. NietMeerIngeschreven mag niet gewijzigd worden.
-- 2. Uitgereikt => DatumUitgifte = CURDATE()
-- 3. NietUitgereikt => DatumUitgifte = NULL
-- ------------------------------------------------------------
CREATE PROCEDURE sp_wijzig_voedselpakket_status(
    IN p_voedselpakket_id BIGINT UNSIGNED,
    IN p_nieuwe_status VARCHAR(50)
)
BEGIN
    DECLARE v_huidige_status VARCHAR(50);
    DECLARE v_is_actief BIT(1);

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    SELECT
        vp.Status,
        vp.IsActief
    INTO
        v_huidige_status,
        v_is_actief
    FROM Voedselpakket AS vp
    WHERE vp.Id = p_voedselpakket_id
    FOR UPDATE;

    IF v_huidige_status IS NULL THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Het geselecteerde voedselpakket bestaat niet (meer).';
    END IF;

    IF v_is_actief <> b'1' THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Het geselecteerde voedselpakket bestaat niet (meer).';
    END IF;

    IF v_huidige_status = 'NietMeerIngeschreven' THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Dit gezin is niet meer ingeschreven bij de voedselbank en daarom kan er geen voedselpakket worden uitgereikt';
    END IF;

    IF p_nieuwe_status NOT IN ('NietUitgereikt', 'Uitgereikt') THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'De geselecteerde status is ongeldig.';
    END IF;

    UPDATE Voedselpakket
    SET
        Status = p_nieuwe_status,
        DatumUitgifte = CASE
            WHEN p_nieuwe_status = 'Uitgereikt' THEN CURDATE()
            ELSE NULL
        END,
        DatumGewijzigd = NOW(6)
    WHERE Id = p_voedselpakket_id;

    COMMIT;

    SELECT 'De wijziging is doorgevoerd' AS Bericht;
END //

DELIMITER ;
