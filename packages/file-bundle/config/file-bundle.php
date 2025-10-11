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
use Rekalogika\File\Association\Contracts\ClassBasedFileLocationResolverInterface;
use Rekalogika\File\Association\Contracts\ObjectManagerInterface;
use Rekalogika\File\Bundle\Command\FileLocationResolverCommand;
use Rekalogika\File\Bundle\DefaultFilesystemFactory;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    //
    // filesystem
    //

    $services
        ->set(DefaultFilesystemFactory::class);

    $services
        ->set('rekalogika.file.default_filesystem')
        ->class(FilesystemOperator::class)
        ->factory([
            service(DefaultFilesystemFactory::class),
            'getDefaultFilesystem',
        ])
    ;


    //
    // commands
    //

    if (interface_exists(ObjectManagerInterface::class)) {
        $services
            ->set('rekalogika.file.command.file_location_resolver')
            ->class(FileLocationResolverCommand::class)
            ->args([
                '$fileLocationResolver' => service(ClassBasedFileLocationResolverInterface::class),
            ])
            ->tag('console.command')
        ;
    }
};
