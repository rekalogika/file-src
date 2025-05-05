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

use Rekalogika\File\Bundle\Debug\FileDataCollector;
use Rekalogika\File\Bundle\Debug\TraceableFilePropertyManager;
use Rekalogika\File\Bundle\Debug\TraceableObjectManager;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services
        ->set('rekalogika.file.association.debug.data_collector')
        ->class(FileDataCollector::class)
        ->tag('data_collector', [
            'id' => 'rekalogika_file',
        ])
        ->tag('kernel.reset', ['method' => 'reset'])
    ;

    $services
        ->set('rekalogika.file.association.debug.traceable_object_manager')
        ->class(TraceableObjectManager::class)
        ->decorate('rekalogika.file.association.object_manager')
        ->args([
            service('.inner'),
            service('debug.stopwatch'),
            service('rekalogika.file.association.debug.data_collector'),
        ])
    ;

    $services
        ->set('rekalogika.file.association.debug.traceable_file_property_manager')
        ->class(TraceableFilePropertyManager::class)
        ->decorate('rekalogika.file.association.file_property_manager')
        ->args([
            service('.inner'),
            service('debug.stopwatch'),
        ])
    ;
};
