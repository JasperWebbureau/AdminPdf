<?php

declare(strict_types=1);

namespace Flexgrid\Modules\AdminPdf\Infrastructure;

use Dompdf\Dompdf;
use Dompdf\Options;
use Flexgrid\Modules\AdminPdf\Contract\DocumentRendererInterface;
use Flexgrid\Modules\AdminPdf\Contract\TemplateResolverInterface;
use Flexgrid\Modules\AdminPdf\ValueObject\PdfDocument;
use Flexgrid\Modules\AdminPdf\ValueObject\PdfRenderOptions;

final class DompdfDocumentRenderer implements DocumentRendererInterface
{
    /** @var TemplateResolverInterface */ private $templates;

    public function __construct(TemplateResolverInterface $templates)
    {
        $this->templates = $templates;
    }

    public function render(
        string $template,
        array $viewModel,
        PdfRenderOptions $options,
        string $fileName
    ): PdfDocument {
        if (!class_exists(Dompdf::class) || !class_exists(Options::class)) {
            throw new \RuntimeException('Dompdf is niet beschikbaar.');
        }

        $this->assertViewModel($viewModel);
        $templatePath = $this->templates->resolve($template);
        $html = $this->renderTemplate($templatePath, $viewModel);
        if (trim($html) === '') {
            throw new \UnexpectedValueException('PDF-template heeft geen HTML opgeleverd.');
        }

        $dompdfOptions = new Options();
        $dompdfOptions->setIsRemoteEnabled($options->allowsRemoteResources());
        $dompdfOptions->setAllowedRemoteHosts($options->getAllowedRemoteHosts());
        $dompdfOptions->setIsPhpEnabled(false);
        $dompdfOptions->setIsJavascriptEnabled(false);
        $dompdfOptions->setDefaultFont($options->getDefaultFont());
        $dompdfOptions->setTempDir(sys_get_temp_dir());
        $dompdfOptions->setFontCache(sys_get_temp_dir());
        $dompdfOptions->setChroot(array_values(array_unique(array_merge(
            [dirname($templatePath)],
            $options->getLocalResourceRoots()
        ))));

        $dompdf = new Dompdf($dompdfOptions);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper($options->getPaperSize(), $options->getOrientation());
        $dompdf->render();
        $content = $dompdf->output();
        if (!is_string($content)) {
            throw new \RuntimeException('Dompdf heeft geen PDF-output opgeleverd.');
        }

        return new PdfDocument(
            $content,
            $fileName,
            (int)$dompdf->getCanvas()->get_page_count()
        );
    }

    private function renderTemplate(string $templatePath, array $viewModel): string
    {
        $renderer = static function (string $path, array $data): string {
            extract($data, EXTR_SKIP);
            ob_start();
            try {
                include $path;
                return (string)ob_get_clean();
            } catch (\Throwable $throwable) {
                ob_end_clean();
                throw $throwable;
            }
        };

        return $renderer($templatePath, $viewModel);
    }

    /** @param mixed $value */
    private function assertViewModel($value, string $path = 'viewModel'): void
    {
        if ($value === null || is_string($value) || is_int($value) || is_float($value) || is_bool($value)) {
            return;
        }
        if (!is_array($value)) {
            throw new \InvalidArgumentException($path . ' mag alleen scalars en arrays bevatten.');
        }
        foreach ($value as $key => $item) {
            if (!is_string($key) && !is_int($key)) {
                throw new \InvalidArgumentException($path . ' bevat een ongeldige sleutel.');
            }
            $this->assertViewModel($item, $path . '[' . (string)$key . ']');
        }
    }
}
