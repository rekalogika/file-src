<?php

declare(strict_types=1);

/*
 * This file is part of rekalogika/file-src package.
 *
 * (c) Priyadi Iman Nurcahyo <https://rekalogika.dev>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

use Rekalogika\Contracts\File\FileRepositoryInterface;
use Rekalogika\File\Zip\DirectoryResourceServer;
use Rekalogika\File\Zip\FileZip;
use Rekalogika\File\Zip\ZipDirectory;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Contracts\Translation\TranslatorInterface;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(FileZip::class)
        ->args([
            service(ZipDirectory::class),
            service(TranslatorInterface::class)
        ]);

    $services->set(ZipDirectory::class)
        ->args([
            service(FileRepositoryInterface::class),
            service(TranslatorInterface::class)->nullOnInvalid(),
        ]);

    $services->set(DirectoryResourceServer::class)
        ->args([
            service(FileZip::class),
            service(TranslatorInterface::class),
        ])
        ->tag('rekalogika.temporary_url.resource_server', [
            'method' => 'respond',
        ])
        ->tag('rekalogika.temporary_url.resource_transformer', [
            'method' => 'transform',
        ]);
};
