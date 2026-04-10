USE Voedselbank_Maaskantje_dag_na_dag_2;

-- ============================================================
-- sp_overzicht_productvoorraden
-- ============================================================
DROP PROCEDURE IF EXISTS sp_overzicht_productvoorraden;

DELIMITER //
CREATE PROCEDURE sp_overzicht_productvoorraden(IN p_categorie VARCHAR(50))
BEGIN
    SELECT
        ppm.Id                          AS ProductPerMagazijnId,
        p.Naam                          AS Productnaam,
        c.Naam                          AS Categorie,
        m.VerpakkingsEenheid            AS Eenheid,
        m.Aantal                        AS Aantal,
        p.Houdbaarheidsdatum            AS Houdbaarheidsdatum,
        ppm.Locatie                     AS Magazijn
    FROM Product p
    INNER JOIN Categorie c ON c.Id = p.CategorieId
    INNER JOIN ProductPerMagazijn ppm ON ppm.ProductId = p.Id
    INNER JOIN Magazijn m ON m.Id = ppm.MagazijnId
    WHERE p.IsActief   = b'1'
      AND c.IsActief   = b'1'
      AND ppm.IsActief = b'1'
      AND m.IsActief   = b'1'
      AND (p_categorie IS NULL OR p_categorie = '' OR c.Naam = p_categorie)
    ORDER BY p.Naam, p.Houdbaarheidsdatum;
END //
DELIMITER ;


-- ============================================================
-- sp_product_details
-- ============================================================
DROP PROCEDURE IF EXISTS sp_product_details;

DELIMITER //
CREATE PROCEDURE sp_product_details(IN p_id INT)
BEGIN
    SELECT
        ppm.Id                          AS ProductPerMagazijnId,
        p.Id                            AS ProductId,
        m.Id                            AS MagazijnId,
        p.Naam                          AS Productnaam,
        p.Barcode                       AS Barcode,
        p.Houdbaarheidsdatum            AS Houdbaarheidsdatum,
        ppm.Locatie                     AS MagazijnLocatie,
        m.Ontvangstdatum                AS Ontvangstdatum,
        m.Uitleveringsdatum             AS Uitleveringsdatum,
        m.Aantal                        AS AantalOpVoorraad,
        m.VerpakkingsEenheid            AS Eenheid,
        c.Naam                          AS Categorie,
        p.Status                        AS Status
    FROM ProductPerMagazijn ppm
    INNER JOIN Product   p ON p.Id = ppm.ProductId
    INNER JOIN Categorie c ON c.Id = p.CategorieId
    INNER JOIN Magazijn  m ON m.Id = ppm.MagazijnId
    WHERE ppm.Id        = p_id
      AND p.IsActief    = b'1'
      AND c.IsActief    = b'1'
      AND ppm.IsActief  = b'1'
      AND m.IsActief    = b'1';
END //
DELIMITER ;


-- ============================================================
-- sp_wijzig_productvoorraad
-- ============================================================
DROP PROCEDURE IF EXISTS sp_wijzig_productvoorraad;

DELIMITER //
CREATE PROCEDURE sp_wijzig_productvoorraad(
    IN p_ppm_id              INT,
    IN p_productnaam         VARCHAR(150),
    IN p_barcode             VARCHAR(20),
    IN p_houdbaarheidsdatum  DATE,
    IN p_status              VARCHAR(50),
    IN p_locatie             VARCHAR(100),
    IN p_ontvangstdatum      DATE,
    IN p_uitleveringsdatum   DATE,
    IN p_aantal              INT,
    IN p_verpakkingseenheid  VARCHAR(50)
)
BEGIN
    UPDATE Product p
    INNER JOIN ProductPerMagazijn ppm ON ppm.ProductId = p.Id
    SET
        p.Naam               = p_productnaam,
        p.Barcode            = p_barcode,
        p.Houdbaarheidsdatum = p_houdbaarheidsdatum,
        p.Status             = p_status
    WHERE ppm.Id = p_ppm_id;

    UPDATE ProductPerMagazijn
    SET Locatie = p_locatie
    WHERE Id = p_ppm_id;

    UPDATE Magazijn m
    INNER JOIN ProductPerMagazijn ppm ON ppm.MagazijnId = m.Id
    SET
        m.Ontvangstdatum     = p_ontvangstdatum,
        m.Uitleveringsdatum  = p_uitleveringsdatum,
        m.Aantal             = p_aantal,
        m.VerpakkingsEenheid = p_verpakkingseenheid
    WHERE ppm.Id = p_ppm_id;
END //
DELIMITER ;