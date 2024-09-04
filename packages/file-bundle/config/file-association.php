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

use Doctrine\Persistence\ManagerRegistry;
use Rekalogika\Contracts\File\FileRepositoryInterface;
use Rekalogika\DirectPropertyAccess\DirectPropertyAccessor;
use Rekalogika\File\Association\Contracts\FileLocationResolverInterface;
use Rekalogika\File\Association\Contracts\ObjectIdResolverInterface;
use Rekalogika\File\Association\Contracts\PropertyInspectorInterface;
use Rekalogika\File\Association\Contracts\PropertyListerInterface;
use Rekalogika\File\Association\Contracts\PropertyReaderInterface;
use Rekalogika\File\Association\Contracts\PropertyWriterInterface;
use Rekalogika\File\Association\FileAssociationManager;
use Rekalogika\File\Association\FileLocationResolver\ChainedFileLocationResolver;
use Rekalogika\File\Association\FileLocationResolver\DefaultFileLocationResolver;
use Rekalogika\File\Association\ObjectIdResolver\ChainedObjectIdResolver;
use Rekalogika\File\Association\ObjectIdResolver\DefaultObjectIdResolver;
use Rekalogika\File\Association\ObjectIdResolver\DoctrineObjectIdResolver;
use Rekalogika\File\Association\PropertyInspector\PropertyInspector;
use Rekalogika\File\Association\PropertyLister\AttributesPropertyLister;
use Rekalogika\File\Association\PropertyLister\ChainPropertyLister;
use Rekalogika\File\Association\PropertyLister\FileAssociationInterfacePropertyLister;
use Rekalogika\File\Association\PropertyReaderWriter\SymfonyPropertyAccessorBridge;
use Rekalogika\File\Association\Reconstitutor\AttributeReconstitutor;
use Rekalogika\File\Association\Reconstitutor\InterfaceReconstitutor;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    //
    // reconstitutors
    //

    $services->set(InterfaceReconstitutor::class)
        ->tag('rekalogika.reconstitutor.class')
        ->args([
            service(FileAssociationManager::class),
        ]);

    $services->set(AttributeReconstitutor::class)
        ->tag('rekalogika.reconstitutor.attribute')
        ->args([
            service(FileAssociationManager::class),
        ]);

    //
    // manager
    //

    $services->set(FileAssociationManager::class)
        ->args([
            '$fileRepository' => service(FileRepositoryInterface::class),
            '$lister' => service(PropertyListerInterface::class),
            '$reader' => service(PropertyReaderInterface::class),
            '$writer' => service(PropertyWriterInterface::class),
            '$inspector' => service(PropertyInspectorInterface::class),
            '$fileLocationResolver' => service(FileLocationResolverInterface::class),
        ]);

    //
    // object id resolver
    //

    $services->alias(
        ObjectIdResolverInterface::class,
        ChainedObjectIdResolver::class,
    );

    $services->set(ChainedObjectIdResolver::class)
        ->args([
            tagged_iterator('rekalogika.file.association.object_id_resolver'),
        ]);

    $services->set(DefaultObjectIdResolver::class)
        ->tag('rekalogika.file.association.object_id_resolver', [
            'priority' => -1000,
        ]);

    if (interface_exists(ManagerRegistry::class)) {
        $services->set(DoctrineObjectIdResolver::class)
            ->args([
                service(ManagerRegistry::class),
            ])
            ->tag('rekalogika.file.association.object_id_resolver', [
                'priority' => -999,
            ]);
    }

    //
    // file location resolver
    //

    $services->alias(
        FileLocationResolverInterface::class,
        ChainedFileLocationResolver::class,
    );

    $services->set(ChainedFileLocationResolver::class)
        ->args([
            tagged_iterator('rekalogika.file.association.file_location_resolver'),
        ]);

    $services->set(DefaultFileLocationResolver::class)
        ->args([
            service(ObjectIdResolverInterface::class),
        ])
        ->tag('rekalogika.file.association.file_location_resolver', [
            'priority' => -1000,
        ]);

    //
    // property lister
    //

    $services->alias(PropertyListerInterface::class, ChainPropertyLister::class);

    $services->set(ChainPropertyLister::class)
        ->args([
            tagged_iterator('rekalogika.file.association.property_lister'),
        ]);

    $services->set(FileAssociationInterfacePropertyLister::class)
        ->tag('rekalogika.file.association.property_lister');

    $services->set(AttributesPropertyLister::class)
        ->tag('rekalogika.file.association.property_lister');

    //
    // property reader writer
    //

    $services->set(SymfonyPropertyAccessorBridge::class)
        ->args([
            service(DirectPropertyAccessor::class),
        ]);

    $services->alias(
        PropertyReaderInterface::class,
        SymfonyPropertyAccessorBridge::class,
    );

    $services->alias(
        PropertyWriterInterface::class,
        SymfonyPropertyAccessorBridge::class,
    );

    //
    // property inspector
    //

    $services->set(PropertyInspectorInterface::class, PropertyInspector::class);
};
