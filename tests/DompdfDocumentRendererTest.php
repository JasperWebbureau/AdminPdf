<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

use Flexgrid\Modules\AdminPdf\Infrastructure\DompdfDocumentRenderer;
use Flexgrid\Modules\AdminPdf\Infrastructure\FilesystemTemplateResolver;
use Flexgrid\Modules\AdminPdf\ValueObject\PdfRenderOptions;

$renderer = new DompdfDocumentRenderer(new FilesystemTemplateResolver([__DIR__ . '/Fixtures']));
$document = $renderer->render('document.php', [
    'title' => 'Vaste PDF-fixture',
    'intro' => 'Renderer zonder businessentity of remote bron.',
    'rows' => [
        ['label' => 'Ontwerp', 'value' => 'EUR 100,00'],
        ['label' => 'Btw', 'value' => 'EUR 21,00'],
    ],
], new PdfRenderOptions(), 'fixture-document.pdf');

adminPdfAssert(strncmp($document->getContent(), '%PDF-', 5) === 0, 'Dompdf-adapter moet echte PDF-bytes opleveren.');
adminPdfAssert($document->getSize() > 500, 'Vaste fixture moet niet-lege PDF-output opleveren.');
adminPdfAssert($document->getFileName() === 'fixture-document.pdf', 'Gewenste bestandsnaam moet behouden blijven.');
adminPdfAssert($document->getPageCount() >= 1, 'Gerenderde fixture moet minimaal één pagina hebben.');

adminPdfAssertThrows(InvalidArgumentException::class, function () use ($renderer): void {
    $renderer->render('document.php', ['title' => new stdClass()], new PdfRenderOptions(), 'invalid.pdf');
}, 'Renderer mag geen businessobjects in het viewmodel accepteren.');

$source = dirname(__DIR__) . '/src';
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source));
foreach ($iterator as $file) {
    if (!$file->isFile() || strtolower($file->getExtension()) !== 'php') {
        continue;
    }
    $contents = (string)file_get_contents($file->getPathname());
    foreach (['AdminInvoice', 'AdminQuote', 'AdminCustomer', 'Webshop', 'App\\Administration'] as $forbidden) {
        adminPdfAssert(strpos($contents, $forbidden) === false, 'AdminPdf bevat verboden businessmodulekoppeling: ' . $forbidden);
    }
}

echo "AdminPdf Dompdf renderer tests passed.\n";
