<?php

declare(strict_types=1);

namespace Flexgrid\Modules\AdminPdf\Contract;

interface TemplateResolverInterface
{
    /**
     * Geeft het absolute, gecontroleerde pad van een template terug.
     */
    public function resolve(string $template): string;
}
