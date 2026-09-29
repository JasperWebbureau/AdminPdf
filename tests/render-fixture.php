<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/bootstrap.php';

use Flexgrid\Modules\AdminPdf\Infrastructure\DompdfDocumentRenderer;
use Flexgrid\Modules\AdminPdf\Infrastructure\FilesystemTemplateResolver;
use Flexgrid\Modules\AdminPdf\ValueObject\PdfRenderOptions;
use Dompdf\Dompdf;
use Dompdf\Options;

$outputDirectory = dirname(__DIR__, 4) . '/tmp/pdfs';
if (!is_dir($outputDirectory) && !mkdir($outputDirectory, 0775, true) && !is_dir($outputDirectory)) {
    throw new RuntimeException('Tijdelijke PDF-map kon niet worden aangemaakt.');
}

$viewModel = [
    'title' => 'AdminPdf rendererfixture',
    'intro' => 'Veilige lokale render zonder businessentity of remote bron.',
    'rows' => [
        ['label' => 'Ontwerp en ontwikkeling', 'value' => 'EUR 100,00'],
        ['label' => 'Btw (21%)', 'value' => 'EUR 21,00'],
        ['label' => 'Totaal', 'value' => 'EUR 121,00'],
    ],
];
$fixtureDirectory = __DIR__ . '/Fixtures';
$document = (new DompdfDocumentRenderer(
    new FilesystemTemplateResolver([__DIR__ . '/Fixtures'])
))->render('document.php', $viewModel, new PdfRenderOptions(), 'adminpdf-fixture.pdf');

$outputPath = $outputDirectory . '/' . $document->getFileName();
if (file_put_contents($outputPath, $document->getContent()) !== $document->getSize()) {
    throw new RuntimeException('Fixture-PDF kon niet volledig worden geschreven.');
}

$renderTemplate = static function (string $path, array $data): string {
    extract($data, EXTR_SKIP);
    ob_start();
    include $path;
    return (string)ob_get_clean();
};
$previewOptions = new Options();
$previewOptions->setPdfBackend('GD');
$previewOptions->setIsRemoteEnabled(false);
$previewOptions->setIsPhpEnabled(false);
$previewOptions->setIsJavascriptEnabled(false);
$previewOptions->setDefaultFont('DejaVu Sans');
$previewOptions->setChroot([$fixtureDirectory]);
$preview = new Dompdf($previewOptions);
$preview->loadHtml($renderTemplate($fixtureDirectory . '/document.php', $viewModel), 'UTF-8');
$preview->setPaper('A4', 'portrait');
$preview->render();
$previewContent = $preview->output(['type' => 'png', 'page' => 1]);
$previewPath = $outputDirectory . '/adminpdf-fixture-page-1.png';
if (!is_string($previewContent) || file_put_contents($previewPath, $previewContent) !== strlen($previewContent)) {
    throw new RuntimeException('Fixture-preview kon niet volledig worden geschreven.');
}

echo $outputPath . PHP_EOL;
echo $previewPath . PHP_EOL;
