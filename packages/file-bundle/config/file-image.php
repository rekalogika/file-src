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

use Rekalogika\File\Image\ImageResizer;
use Rekalogika\File\Image\ImageTwigExtension;
use Rekalogika\File\Image\ImageTwigRuntime;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services
        ->set(ImageResizer::class)
        ->tag('rekalogika.file.derivation.filter');

    $services
        ->set('rekalogika.file.image.twig_extension')
        ->class(ImageTwigExtension::class)
        ->tag('twig.extension', [
            'priority' => 1000000,
        ]);

    $services
        ->set('rekalogika.file.image.twig_runtime')
        ->class(ImageTwigRuntime::class)
        ->args([
            service(ImageResizer::class),
        ])
        ->tag('twig.runtime', [
            'priority' => 1000000,
        ]);
};
