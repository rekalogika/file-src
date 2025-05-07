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
use PHPUnit\Metadata\Metadata;
use Rekalogika\Contracts\File\FileRepositoryInterface;
use Rekalogika\File\Association\ClassBasedFileLocationResolver\ChainedClassBasedFileLocationResolver;
use Rekalogika\File\Association\ClassBasedFileLocationResolver\DefaultClassBasedFileLocationResolver;
use Rekalogika\File\Association\ClassMetadataFactory\CachingClassMetadataFactory;
use Rekalogika\File\Association\ClassMetadataFactory\DefaultClassMetadataFactory;
use Rekalogika\File\Association\ClassSignatureResolver\AttributeClassSignatureResolver;
use Rekalogika\File\Association\ClassSignatureResolver\ChainClassSignatureResolver;
use Rekalogika\File\Association\ClassSignatureResolver\DefaultClassSignatureResolver;
use Rekalogika\File\Association\ClassSignatureResolver\MetadataClassSignatureResolver;
use Rekalogika\File\Association\Contracts\ClassBasedFileLocationResolverInterface;
use Rekalogika\File\Association\Contracts\ClassSignatureResolverInterface;
use Rekalogika\File\Association\Contracts\ObjectIdResolverInterface;
use Rekalogika\File\Association\FilePropertyManager\DefaultFilePropertyManager;
use Rekalogika\File\Association\FilePropertyManager\LoggingFilePropertyManager;
use Rekalogika\File\Association\ObjectIdResolver\ChainedObjectIdResolver;
use Rekalogika\File\Association\ObjectIdResolver\DefaultObjectIdResolver;
use Rekalogika\File\Association\ObjectIdResolver\DoctrineObjectIdResolver;
use Rekalogika\File\Association\ObjectManager\DefaultObjectManager;
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

    $services
        ->set('rekalogika.file.association.reconstitutor.interface')
        ->class(InterfaceReconstitutor::class)
        ->args([
            service('rekalogika.file.association.object_manager'),
        ])
        ->tag('rekalogika.reconstitutor.class')
    ;

    $services
        ->set('rekalogika.file.association.reconstitutor.attribute')
        ->class(AttributeReconstitutor::class)
        ->args([
            service('rekalogika.file.association.object_manager'),
        ])
        ->tag('rekalogika.reconstitutor.attribute')
    ;

    //
    // object manager
    //

    $services->alias(
        'rekalogika.file.association.object_manager',
        'rekalogika.file.association.object_manager.default',
    );

    $services
        ->set('rekalogika.file.association.object_manager.default')
        ->class(DefaultObjectManager::class)
        ->args([
            '$classMetadataFactory' => service('rekalogika.file.association.class_metadata_factory'),
            '$objectIdResolver' => service(ObjectIdResolverInterface::class),
            '$filePropertyManager' => service('rekalogika.file.association.file_property_manager'),
        ])
    ;

    //
    // property manager
    //

    $services->alias(
        'rekalogika.file.association.file_property_manager',
        'rekalogika.file.association.file_property_manager.default',
    );

    $services
        ->set('rekalogika.file.association.file_property_manager.default')
        ->class(DefaultFilePropertyManager::class)
        ->args([
            '$fileRepository' => service(FileRepositoryInterface::class),
            '$fileLocationResolver' => service(ClassBasedFileLocationResolverInterface::class),
        ])
        ->tag('kernel.reset', [
            'method' => 'reset',
        ])
    ;

    $services
        ->set('rekalogika.file.association.file_property_manager.logging')
        ->class(LoggingFilePropertyManager::class)
        ->decorate('rekalogika.file.association.file_property_manager')
        ->args([
            service('.inner'),
            service('logger')->nullOnInvalid(),
        ])
        ->tag('monolog.logger', ['channel' => 'rekalogika.file'])
    ;

    //
    // object id resolver
    //

    $services->alias(
        ObjectIdResolverInterface::class,
        'rekalogika.file.association.object_id_resolver.chained',
    );

    $services
        ->set('rekalogika.file.association.object_id_resolver.chained')
        ->class(ChainedObjectIdResolver::class)
        ->args([
            tagged_iterator('rekalogika.file.association.object_id_resolver'),
        ])
    ;

    $services
        ->set('rekalogika.file.association.object_id_resolver.default')
        ->class(DefaultObjectIdResolver::class)
        ->tag('rekalogika.file.association.object_id_resolver', [
            'priority' => -1000,
        ])
    ;

    if (interface_exists(ManagerRegistry::class)) {
        $services
            ->set(DoctrineObjectIdResolver::class)
            ->args([
                service(ManagerRegistry::class),
            ])
            ->tag('rekalogika.file.association.object_id_resolver', [
                'priority' => -999,
            ])
        ;
    }

    //
    // class-based file location resolver
    //

    $services->alias(
        ClassBasedFileLocationResolverInterface::class,
        'rekalogika.file.association.class_based_file_location_resolver.chained',
    );

    $services
        ->set('rekalogika.file.association.class_based_file_location_resolver.chained')
        ->class(ChainedClassBasedFileLocationResolver::class)
        ->args([
            tagged_iterator('rekalogika.file.association.class_based_file_location_resolver'),
        ])
    ;

    $services
        ->set('rekalogika.file.association.class_based_file_location_resolver.default')
        ->class(DefaultClassBasedFileLocationResolver::class)
        ->args([
            service(ClassSignatureResolverInterface::class),
        ])
        ->tag('rekalogika.file.association.class_based_file_location_resolver', [
            'priority' => -1000,
        ])
    ;

    //
    // class signature resolver, user-facing
    //

    $services
        ->set(ClassSignatureResolverInterface::class)
        ->class(MetadataClassSignatureResolver::class)
        ->args([
            service('rekalogika.file.association.class_metadata_factory'),
        ])
    ;

    //
    // class signature resolver, backend
    //

    $services
        ->set('rekalogika.file.association.class_signature_resolver.chained')
        ->class(ChainClassSignatureResolver::class)
        ->args([
            tagged_iterator('rekalogika.file.association.class_signature_resolver'),
        ])
    ;

    $services
        ->set('rekalogika.file.association.class_signature_resolver.default')
        ->class(DefaultClassSignatureResolver::class)
        ->tag('rekalogika.file.association.class_signature_resolver', [
            'priority' => -1000,
        ])
    ;

    $services
        ->set('rekalogika.file.association.class_signature_resolver.attribute')
        ->class(AttributeClassSignatureResolver::class)
        ->tag('rekalogika.file.association.class_signature_resolver', [
            'priority' => -999,
        ])
    ;

    //
    // property lister
    //

    $services->alias(
        'rekalogika.file.association.property_lister',
        'rekalogika.file.association.property_lister.chain',
    );

    $services
        ->set('rekalogika.file.association.property_lister.chain')
        ->class(ChainPropertyLister::class)
        ->args([
            tagged_iterator('rekalogika.file.association.property_lister'),
        ])
    ;

    $services
        ->set('rekalogika.file.association.property_lister.file_association_interface')
        ->class(FileAssociationInterfacePropertyLister::class)
        ->tag('rekalogika.file.association.property_lister')
    ;

    $services
        ->set('rekalogika.file.association.property_lister.attributes')
        ->class(AttributesPropertyLister::class)
        ->tag('rekalogika.file.association.property_lister')
    ;

    //
    // class metadata factory
    //

    $services->alias(
        'rekalogika.file.association.class_metadata_factory',
        'rekalogika.file.association.class_metadata_factory.default',
    );

    $services
        ->set('rekalogika.file.association.class_metadata_factory.default')
        ->class(DefaultClassMetadataFactory::class)
        ->args([
            service('rekalogika.file.association.property_lister'),
            service('rekalogika.file.association.class_signature_resolver.chained'),
        ])
    ;

    $services
        ->set('rekalogika.file.association.class_metadata_factory.cache')
        ->parent('cache.system')
        ->tag('cache.pool')
    ;

    $services
        ->set('rekalogika.file.association.class_metadata_factory.caching')
        ->class(CachingClassMetadataFactory::class)
        ->decorate('rekalogika.file.association.class_metadata_factory')
        ->args([
            service('.inner'),
            service('rekalogika.file.association.class_metadata_factory.cache'),
        ])
    ;
};
