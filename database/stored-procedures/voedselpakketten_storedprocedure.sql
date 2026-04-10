-- ------------------------------------------
-- 5) Stored Procedures (User Story 03)
-- ------------------------------------------

DROP PROCEDURE IF EXISTS sp_is_manager;
DROP PROCEDURE IF EXISTS sp_get_eetwensen;
DROP PROCEDURE IF EXISTS sp_overzicht_voedselpakketten;

DELIMITER //

CREATE PROCEDURE sp_is_manager(IN p_gebruiker_id BIGINT UNSIGNED)
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
                  AND r.Naam = 'Manager'
            ) THEN 1
            ELSE 0
        END AS IsManager;
END //

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

DELIMITER ;
