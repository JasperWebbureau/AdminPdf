# Changelog

## 0.2.0 - 2026-09-19

- Generieke `ProjectDocumentBranding` toegevoegd voor veilige bedrijfsgegevens en lokale logo-data-URI's.
- Generieke `ScssBrandColorReader` toegevoegd voor consistente primary/secondary-kleuren met veilige defaults.
- De services blijven documenttype-onafhankelijk en worden als eerste gebruikt door AdminQuote.

## 0.1.0 - 2026-09-18

- Generieke renderer- en templateresolvercontracts toegevoegd.
- Immutable PDF-document- en veilige renderoptie-value objects toegevoegd.
- Veilige Dompdf-adapter met standaard uitgeschakelde remote resources toegevoegd.
- Filesystemresolver met template-rootisolatie en traversalbeveiliging toegevoegd.
- Vaste rendererfixture en PHP 7.3-tests voor output, isolatie en veiligheidsdefaults toegevoegd.
