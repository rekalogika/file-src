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
use Rekalogika\File\Association\ClassBasedFileLocationResolver\ChainedClassBasedFileLocationResolver;
use Rekalogika\File\Association\ClassBasedFileLocationResolver\DefaultClassBasedFileLocationResolver;
use Rekalogika\File\Association\ClassMetadataFactory\CachingClassMetadataFactory;
use Rekalogika\File\Association\ClassMetadataFactory\DefaultClassMetadataFactory;
use Rekalogika\File\Association\ClassSignatureResolver\AttributeClassSignatureResolver;
use Rekalogika\File\Association\ClassSignatureResolver\ChainClassSignatureResolver;
use Rekalogika\File\Association\ClassSignatureResolver\DefaultClassSignatureResolver;
use Rekalogika\File\Association\Command\FileLocationResolverCommand;
use Rekalogika\File\Association\Contracts\ClassBasedFileLocationResolverInterface;
use Rekalogika\File\Association\Contracts\ClassMetadataFactoryInterface;
use Rekalogika\File\Association\Contracts\ClassSignatureResolverInterface;
use Rekalogika\File\Association\Contracts\FilePropertyManagerInterface;
use Rekalogika\File\Association\Contracts\ObjectIdResolverInterface;
use Rekalogika\File\Association\Contracts\PropertyListerInterface;
use Rekalogika\File\Association\FileAssociationManager;
use Rekalogika\File\Association\FilePropertyManager\DefaultFilePropertyManager;
use Rekalogika\File\Association\ObjectIdResolver\ChainedObjectIdResolver;
use Rekalogika\File\Association\ObjectIdResolver\DefaultObjectIdResolver;
use Rekalogika\File\Association\ObjectIdResolver\DoctrineObjectIdResolver;
use Rekalogika\File\Association\PropertyLister\AttributesPropertyLister;
use Rekalogika\File\Association\PropertyLister\ChainPropertyLister;
use Rekalogika\File\Association\PropertyLister\FileAssociationInterfacePropertyLister;
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
        ->args([
            service(FileAssociationManager::class),
        ])
        ->tag('rekalogika.reconstitutor.class')
    ;

    $services->set(AttributeReconstitutor::class)
        ->args([
            service(FileAssociationManager::class),
        ])
        ->tag('rekalogika.reconstitutor.attribute')
    ;

    //
    // manager
    //

    $services
        ->set(FileAssociationManager::class)
        ->args([
            '$classMetadataFactory' => service(ClassMetadataFactoryInterface::class),
            '$objectIdResolver' => service(ObjectIdResolverInterface::class),
            '$filePropertyManager' => service(FilePropertyManagerInterface::class),
        ]);

    $services
        ->set(FilePropertyManagerInterface::class)
        ->class(DefaultFilePropertyManager::class)
        ->args([
            '$fileRepository' => service(FileRepositoryInterface::class),
            '$fileLocationResolver' => service(ClassBasedFileLocationResolverInterface::class),
        ])
        ->call('setLogger', [service('logger')->ignoreOnInvalid()])
        ->tag('monolog.logger', ['channel' => 'rekalogika.file'])
    ;

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
    // class-based file location resolver
    //

    $services
        ->set(ClassBasedFileLocationResolverInterface::class)
        ->class(ChainedClassBasedFileLocationResolver::class)
        ->args([
            tagged_iterator('rekalogika.file.association.class_based_file_location_resolver'),
        ]);

    $services
        ->set(DefaultClassBasedFileLocationResolver::class)
        ->args([
            service(ClassMetadataFactoryInterface::class),
        ])
        ->tag('rekalogika.file.association.class_based_file_location_resolver', [
            'priority' => -1000,
        ]);

    //
    // class signature resolver
    //

    $services
        ->set(ClassSignatureResolverInterface::class)
        ->class(ChainClassSignatureResolver::class)
        ->args([
            tagged_iterator('rekalogika.file.association.class_signature_resolver'),
        ]);

    $services
        ->set(DefaultClassSignatureResolver::class)
        ->tag('rekalogika.file.association.class_signature_resolver', [
            'priority' => -1000,
        ]);

    $services
        ->set(AttributeClassSignatureResolver::class)
        ->tag('rekalogika.file.association.class_signature_resolver', [
            'priority' => -999,
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
    // class metadata factory
    //

    $services
        ->set(ClassMetadataFactoryInterface::class)
        ->class(DefaultClassMetadataFactory::class)
        ->args([
            service(PropertyListerInterface::class),
            service(ClassSignatureResolverInterface::class),
        ]);

    $services
        ->set('rekalogika.file.association.class_metadata_factory.cache')
        ->parent('cache.system')
        ->tag('cache.pool');

    $services
        ->set(CachingClassMetadataFactory::class)
        ->decorate(ClassMetadataFactoryInterface::class)
        ->args([
            service('.inner'),
            service('rekalogika.file.association.class_metadata_factory.cache'),
        ]);

    //
    // commands
    //

    $services->set(FileLocationResolverCommand::class)
        ->args([
            '$fileLocationResolver' => service(ClassBasedFileLocationResolverInterface::class),
        ])
        ->tag('console.command');
};
