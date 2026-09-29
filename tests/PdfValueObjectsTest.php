<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

use Flexgrid\Modules\AdminPdf\ValueObject\PdfDocument;
use Flexgrid\Modules\AdminPdf\ValueObject\PdfRenderOptions;

$defaults = new PdfRenderOptions();
adminPdfAssert(!$defaults->allowsRemoteResources(), 'Remote PDF-resources moeten standaard uit staan.');
adminPdfAssert($defaults->getPaperSize() === 'A4', 'Standaardpapier moet A4 zijn.');
adminPdfAssert($defaults->getOrientation() === 'portrait', 'Standaardoriëntatie moet portrait zijn.');

$remote = new PdfRenderOptions('A4', 'landscape', 'DejaVu Sans', [], ['cdn.example.test']);
adminPdfAssert($remote->allowsRemoteResources(), 'Expliciete hostallowlist moet remote resources inschakelen.');
adminPdfAssert($remote->getAllowedRemoteHosts() === ['cdn.example.test'], 'Remote hostallowlist moet stabiel blijven.');

$document = new PdfDocument('%PDF-1.7 fixture', 'test-document.pdf', 1);
adminPdfAssert($document->getMimeType() === 'application/pdf', 'PdfDocument moet het PDF-mimetype leveren.');
adminPdfAssert($document->getSize() === strlen('%PDF-1.7 fixture'), 'PdfDocument moet bytegrootte exact leveren.');
adminPdfAssert(strlen($document->getSha256()) === 64, 'PdfDocument moet een SHA-256 fingerprint leveren.');

adminPdfAssertThrows(InvalidArgumentException::class, function (): void {
    new PdfRenderOptions('A4', 'portrait', 'DejaVu Sans', [], ['https://example.test']);
}, 'Remote allowlist accepteert alleen hostnamen, geen URLs.');
adminPdfAssertThrows(InvalidArgumentException::class, function (): void {
    new PdfDocument('geen pdf', '../factuur.pdf', 1);
}, 'PdfDocument moet ongeldige content en bestandsnamen weigeren.');

echo "AdminPdf value object tests passed.\n";
