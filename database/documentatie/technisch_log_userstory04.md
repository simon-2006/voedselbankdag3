# Technische log - User Story 04

## Probleem
De website viel uit op pagina's die stored procedures aanroepen. In `storage/logs/laravel.log` stonden fouten zoals:
- `PROCEDURE ... sp_is_manager does not exist`
- `PROCEDURE ... sp_allergie_overzicht_allergieen does not exist`

## Oorzaak
De database was via SQL-script gevuld, maar migraties/stored procedures waren niet (volledig) uitgevoerd.

## Oplossing
1. Backend robuust gemaakt met fallback-queries (JOIN's) als een stored procedure ontbreekt.
2. User Story 04 volledig toegevoegd in controller/routes/views.
3. Nieuwe stored procedure scriptset opgeleverd in:
   - `database/stored-procedures/voedselpakketten_storedprocedure.sql`
4. Logging toegevoegd rond:
   - laden overzichten
   - openen statusformulier
   - statuswijziging succes/fout

## Verwacht resultaat
- De pagina's laden ook als procedures nog niet aanwezig zijn.
- Als procedures wel geladen zijn, draait de app via de procedurelaag.
- Scenario 01 en 02 van User Story 04 zijn functioneel afgedekt.
