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

use League\Flysystem\FilesystemOperator;
use Rekalogika\File\Tests\Factory;
use Rekalogika\File\Tests\TestKernel;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    // mock urlgenerator
    $services->set(UrlGeneratorInterface::class)
        ->factory([Factory::class, 'createUrlGenerator']);

    // add test aliases
    $serviceIds = TestKernel::getServiceIds();

    // foreach ($serviceIds as $serviceId) {
    //     $services->set($serviceId)->public();
    // }

    // filesystem for testing
    $services->set('test.filesystem', FilesystemOperator::class)
        ->factory([Factory::class, 'createTestFilesystem'])
        ->tag('rekalogika.file.filesystem', ['identifier' => 'default'])
        ->public();
};
