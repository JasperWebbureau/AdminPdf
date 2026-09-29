<!doctype html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <style>
        body { color: #18213f; font-family: "DejaVu Sans", sans-serif; font-size: 12px; }
        h1 { border-bottom: 2px solid #18213f; font-size: 24px; padding-bottom: 8px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border-bottom: 1px solid #d7dbe3; padding: 7px; text-align: left; }
        .right { text-align: right; }
    </style>
</head>
<body>
<h1><?=htmlspecialchars((string)$title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')?></h1>
<p><?=htmlspecialchars((string)$intro, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')?></p>
<table>
    <thead><tr><th>Omschrijving</th><th class="right">Bedrag</th></tr></thead>
    <tbody>
    <?php foreach ($rows as $row) { ?>
        <tr><td><?=htmlspecialchars((string)$row['label'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')?></td><td class="right"><?=htmlspecialchars((string)$row['value'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')?></td></tr>
    <?php } ?>
    </tbody>
</table>
</body>
</html>
