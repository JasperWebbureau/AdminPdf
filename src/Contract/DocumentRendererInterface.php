<?php

declare(strict_types=1);

namespace Flexgrid\Modules\AdminPdf\Contract;

use Flexgrid\Modules\AdminPdf\ValueObject\PdfDocument;
use Flexgrid\Modules\AdminPdf\ValueObject\PdfRenderOptions;

interface DocumentRendererInterface
{
    public function render(
        string $template,
        array $viewModel,
        PdfRenderOptions $options,
        string $fileName
    ): PdfDocument;
}
