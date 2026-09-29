<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

use Flexgrid\Modules\AdminPdf\Infrastructure\FilesystemTemplateResolver;

$resolver = new FilesystemTemplateResolver([__DIR__ . '/Fixtures']);
$resolved = $resolver->resolve('document.php');
adminPdfAssert(is_file($resolved), 'Resolver moet een template binnen een toegestane root vinden.');
adminPdfAssert(basename($resolved) === 'document.php', 'Resolver moet het bedoelde fixturepad teruggeven.');

adminPdfAssertThrows(InvalidArgumentException::class, function () use ($resolver): void {
    $resolver->resolve('../bootstrap.php');
}, 'Resolver moet directory traversal weigeren.');
adminPdfAssertThrows(RuntimeException::class, function () use ($resolver): void {
    $resolver->resolve('ontbreekt.php');
}, 'Resolver moet een ontbrekend template expliciet melden.');

echo "AdminPdf template resolver tests passed.\n";
