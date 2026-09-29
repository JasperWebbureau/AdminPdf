<?php

declare(strict_types=1);

namespace Flexgrid\Modules\AdminPdf\Infrastructure;

use Flexgrid\Modules\AdminPdf\Contract\TemplateResolverInterface;

final class FilesystemTemplateResolver implements TemplateResolverInterface
{
    /** @var string[] */ private $roots;

    /** @param string[] $roots */
    public function __construct(array $roots)
    {
        if ($roots === []) {
            throw new \InvalidArgumentException('Minimaal één PDF-template-root is verplicht.');
        }

        $this->roots = [];
        foreach ($roots as $root) {
            if (!is_string($root) || trim($root) === '') {
                throw new \InvalidArgumentException('PDF-template-root moet een bestaand pad zijn.');
            }
            $resolved = realpath($root);
            if ($resolved === false || !is_dir($resolved)) {
                throw new \InvalidArgumentException('PDF-template-root bestaat niet.');
            }
            $this->roots[] = rtrim($resolved, '/\\');
        }
        $this->roots = array_values(array_unique($this->roots));
    }

    public function resolve(string $template): string
    {
        $template = trim(str_replace('\\', '/', $template));
        if ($template === ''
            || strpos($template, "\0") !== false
            || preg_match('#(^/|^[A-Za-z]:|(^|/)\.\.(/|$))#', $template) === 1
        ) {
            throw new \InvalidArgumentException('PDF-templatepad is ongeldig.');
        }

        foreach ($this->roots as $root) {
            $candidate = realpath($root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $template));
            if ($candidate !== false && is_file($candidate) && $this->isWithinRoot($candidate, $root)) {
                return $candidate;
            }
        }

        throw new \RuntimeException('PDF-template niet gevonden.');
    }

    private function isWithinRoot(string $path, string $root): bool
    {
        if (DIRECTORY_SEPARATOR === '\\') {
            $path = strtolower($path);
            $root = strtolower($root);
        }

        return $path === $root || strpos($path, $root . DIRECTORY_SEPARATOR) === 0;
    }
}
