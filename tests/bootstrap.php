<?php

declare(strict_types=1);

$moduleRoot = dirname(__DIR__);
$projectRoot = dirname(__DIR__, 4);

require_once $projectRoot . '/vendor/autoload.php';
require_once $moduleRoot . '/src/Contract/DocumentRendererInterface.php';
require_once $moduleRoot . '/src/Contract/TemplateResolverInterface.php';
require_once $moduleRoot . '/src/ValueObject/PdfRenderOptions.php';
require_once $moduleRoot . '/src/ValueObject/PdfDocument.php';
require_once $moduleRoot . '/src/Infrastructure/FilesystemTemplateResolver.php';
require_once $moduleRoot . '/src/Infrastructure/DompdfDocumentRenderer.php';

function adminPdfAssert($condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

function adminPdfAssertThrows(string $exceptionClass, callable $callback, string $message): void
{
    try {
        $callback();
    } catch (Throwable $throwable) {
        if ($throwable instanceof $exceptionClass) {
            return;
        }
        throw new RuntimeException($message . ' Ontvangen: ' . get_class($throwable));
    }
    throw new RuntimeException($message . ' Er werd geen exception gegooid.');
}
