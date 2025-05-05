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
use Rekalogika\File\FileFactory;
use Rekalogika\File\Repository\FileRepository;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services
        ->set(FileFactory::class);

    $services
        ->set(FileRepositoryInterface::class)
        ->class(FileRepository::class)
        ->factory([
            service(FileFactory::class),
            'getFileRepository',
        ])
        ->tag('kernel.reset', ['method' => 'reset'])
    ;
};
