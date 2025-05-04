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

use Rekalogika\File\Association\Contracts\FilePropertyManagerInterface;
use Rekalogika\File\Association\Contracts\ObjectManagerInterface;
use Rekalogika\File\Bundle\Debug\TraceableFilePropertyManager;
use Rekalogika\File\Bundle\Debug\TraceableObjectManager;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services
        ->set(TraceableObjectManager::class)
        ->decorate(ObjectManagerInterface::class)
        ->args([
            service('.inner'),
            service('debug.stopwatch'),
        ]);

    $services
        ->set(TraceableFilePropertyManager::class)
        ->decorate(FilePropertyManagerInterface::class)
        ->args([
            service('.inner'),
            service('debug.stopwatch'),
        ]);
};
