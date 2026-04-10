DELIMITER //
CREATE PROCEDURE sp_GetProductenPerLeverancier(IN p_leverancierId INT)
BEGIN
    -- Exameneis: Gebruik van specifieke tabellen voor US_08
    -- Tabellen: Product, ProductPerLeverancier, Leverancier, Contact (via koppeltabel)
    SELECT 
        p.Id, 
        p.Naam, 
        p.SoortAllergie, 
        p.Barcode, 
        p.Houdbaarheidsdatum,
        l.Naam AS LeverancierNaam,
        l.LeverancierNummer,
        l.LeverancierType,
        c.Email AS ContactEmail
    FROM 
        Product p
    INNER JOIN 
        ProductPerLeverancier ppl ON p.Id = ppl.ProductId
    INNER JOIN 
        Leverancier l ON ppl.LeverancierId = l.Id
    LEFT JOIN 
        ContactPerLeverancier cpl ON l.Id = cpl.LeverancierId
    LEFT JOIN 
        Contact c ON cpl.ContactId = c.Id
    WHERE 
        ppl.LeverancierId = p_leverancierId;
END //
DELIMITER ;sp_GetProductenPerLeveranciersp_GetProductenPerLeverancier