<?php

declare(strict_types=1);

namespace Flexgrid\Modules\AdminPdf\ValueObject;

final class PdfDocument
{
    /** @var string */ private $content;
    /** @var string */ private $fileName;
    /** @var int */ private $pageCount;

    public function __construct(string $content, string $fileName, int $pageCount)
    {
        $fileName = trim($fileName);
        if (strncmp($content, '%PDF-', 5) !== 0) {
            throw new \InvalidArgumentException('Document bevat geen geldige PDF-header.');
        }
        if ($fileName === ''
            || strlen($fileName) > 124
            || preg_match('/^[A-Za-z0-9][A-Za-z0-9._ -]*\.pdf$/D', $fileName) !== 1
        ) {
            throw new \InvalidArgumentException('PDF-bestandsnaam is ongeldig.');
        }
        if ($pageCount < 1) {
            throw new \InvalidArgumentException('PDF moet minimaal één pagina bevatten.');
        }

        $this->content = $content;
        $this->fileName = $fileName;
        $this->pageCount = $pageCount;
    }

    public function getContent(): string { return $this->content; }
    public function getFileName(): string { return $this->fileName; }
    public function getMimeType(): string { return 'application/pdf'; }
    public function getPageCount(): int { return $this->pageCount; }
    public function getSize(): int { return strlen($this->content); }
    public function getSha256(): string { return hash('sha256', $this->content); }
}
