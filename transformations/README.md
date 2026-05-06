# Transformations

Šiame kataloge laikomos projektinės transformacijos, kurios nėra SQL migracijos, bet keičia architektūrą, standartus ar modulinius šablonus.

Formatas:
- vienas pokytis = vienas `.todo` failas
- pavadinimo formatas: `YYYY-MM-DD-N trumpas pavadinimas.todo`
- failo viduje aprašoma:
  - tikslas
  - apimtis
  - kokie failai/sluoksniai keičiami
  - kaip atpažinti kad transformacija užbaigta

Veikimo principas:
- `system/tools` skaito `transformations/*.todo`
- kaip ir SQL kataloge, rodomi tik failai, kurie yra „naujesni“ už `gwcms/last_transformations`
- kai transformacijos peržiūrėtos / įgyvendintos, admin dalyje galima pažymėti jas kaip matytas

Kada dėti čia, o ne į `sql/`:
- jei keičiasi PHP/Smarty/JS architektūra ar standartizacija
- jei pokytis yra procesinis ar refaktorinis
- jei nereikia vykdomo SQL

Kada nedėti čia:
- jei pokytis yra DB schema ar duomenų migracija, tada jis turi eiti į `sql/`

Šio katalogo paskirtis yra turėti aiškų, chronologinį „techninių transformacijų backlogą“, panašų į SQL migracijų katalogą.
