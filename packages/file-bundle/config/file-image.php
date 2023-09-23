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

use Psr\Container\ContainerInterface;
use Rekalogika\File\Image\ImageResizer;
use Rekalogika\File\Image\ImageTwigExtension;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(ImageResizer::class)
        ->tag('rekalogika.file.derivation.filter');

    $services->set(ImageTwigExtension::class)
        ->tag('container.service_subscriber')
        ->tag('twig.extension', [
            'priority' => 1000000,
        ])
        ->call('setContainer', [
            service(ContainerInterface::class),
        ]);
};
