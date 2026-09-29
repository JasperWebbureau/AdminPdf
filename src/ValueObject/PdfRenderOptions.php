<?php

declare(strict_types=1);

namespace Flexgrid\Modules\AdminPdf\ValueObject;

final class PdfRenderOptions
{
    /** @var string */ private $paperSize;
    /** @var string */ private $orientation;
    /** @var string */ private $defaultFont;
    /** @var string[] */ private $localResourceRoots;
    /** @var string[] */ private $allowedRemoteHosts;

    /**
     * @param string[] $localResourceRoots
     * @param string[] $allowedRemoteHosts
     */
    public function __construct(
        string $paperSize = 'A4',
        string $orientation = 'portrait',
        string $defaultFont = 'DejaVu Sans',
        array $localResourceRoots = [],
        array $allowedRemoteHosts = []
    ) {
        $paperSize = strtoupper(trim($paperSize));
        $orientation = strtolower(trim($orientation));
        $defaultFont = trim($defaultFont);
        if (!in_array($paperSize, ['A3', 'A4', 'A5', 'LETTER', 'LEGAL'], true)) {
            throw new \InvalidArgumentException('Niet-ondersteund PDF-papierformaat.');
        }
        if (!in_array($orientation, ['portrait', 'landscape'], true)) {
            throw new \InvalidArgumentException('PDF-oriëntatie moet portrait of landscape zijn.');
        }
        if ($defaultFont === '' || strlen($defaultFont) > 80) {
            throw new \InvalidArgumentException('PDF-standaardlettertype is ongeldig.');
        }

        $this->paperSize = $paperSize;
        $this->orientation = $orientation;
        $this->defaultFont = $defaultFont;
        $this->localResourceRoots = $this->normalizeRoots($localResourceRoots);
        $this->allowedRemoteHosts = $this->normalizeHosts($allowedRemoteHosts);
    }

    public function getPaperSize(): string { return $this->paperSize; }
    public function getOrientation(): string { return $this->orientation; }
    public function getDefaultFont(): string { return $this->defaultFont; }
    /** @return string[] */ public function getLocalResourceRoots(): array { return $this->localResourceRoots; }
    /** @return string[] */ public function getAllowedRemoteHosts(): array { return $this->allowedRemoteHosts; }
    public function allowsRemoteResources(): bool { return $this->allowedRemoteHosts !== []; }

    /** @param mixed[] $roots @return string[] */
    private function normalizeRoots(array $roots): array
    {
        $normalized = [];
        foreach ($roots as $root) {
            if (!is_string($root) || trim($root) === '') {
                throw new \InvalidArgumentException('Lokale PDF-resource-root moet een bestaand pad zijn.');
            }
            $resolved = realpath($root);
            if ($resolved === false || !is_dir($resolved)) {
                throw new \InvalidArgumentException('Lokale PDF-resource-root bestaat niet.');
            }
            $normalized[] = rtrim($resolved, '/\\');
        }

        return array_values(array_unique($normalized));
    }

    /** @param mixed[] $hosts @return string[] */
    private function normalizeHosts(array $hosts): array
    {
        $normalized = [];
        foreach ($hosts as $host) {
            if (!is_string($host)) {
                throw new \InvalidArgumentException('Toegestane remote PDF-host moet tekst zijn.');
            }
            $host = strtolower(trim($host));
            if ($host === '' || filter_var($host, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME) === false) {
                throw new \InvalidArgumentException('Ongeldige toegestane remote PDF-host.');
            }
            $normalized[] = $host;
        }

        return array_values(array_unique($normalized));
    }
}
