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

namespace Rekalogika\File\Bundle\DependencyInjection;

use Rekalogika\File\Association\Contracts\FileLocationResolverInterface;
use Rekalogika\File\Association\Contracts\ObjectIdResolverInterface;
use Rekalogika\File\Association\Contracts\PropertyListerInterface;
use Rekalogika\File\Association\FileAssociationManager;
use Rekalogika\File\Bridge\Symfony\HttpFoundation\FromHttpFoundationFileAdapter;
use Rekalogika\File\Bundle\DefaultFilesystemFactory;
use Rekalogika\File\FileFactory;
use Rekalogika\File\Derivation\Filter\FileFilterInterface;
use Rekalogika\File\Image\ImageResizer;
use Rekalogika\File\Server\FileInterfaceResourceServer;
use Rekalogika\File\Tests\TestKernel;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Reference;

class RekalogikaFileExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new PhpFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../../config')
        );

        //
        // load service configurations
        //

        $loader->load('file.php');
        $loader->load('file-bundle.php');

        if (class_exists(FileAssociationManager::class)) {
            $loader->load('file-association.php');
        }

        if (class_exists(ImageResizer::class)) {
            $loader->load('file-image.php');
        }

        if (class_exists(FileInterfaceResourceServer::class)) {
            $loader->load('file-server.php');
        }

        //
        // load service configuration for test environment
        //

        $env = $container->getParameter('kernel.environment');

        if ($env === 'test' && class_exists(TestKernel::class)) {
            $loader->load('tests.php');
        }

        //
        // use config to configure services
        //

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        // default filesystem directory
        $container->getDefinition(DefaultFilesystemFactory::class)
            ->setArgument(0, $config['default_filesystem_directory']);

        // filesystems
        /** @var array<string,string> */
        $filesystems = $config['filesystems'] ?? [];

        if (count($filesystems) === 0) {
            $filesystems = [
                'default' => 'rekalogika.file.default_filesystem'
            ];
        }

        $newFilesystems = [];
        foreach ($filesystems as $name => $serviceId) {
            $newFilesystems[$name] = new Reference($serviceId);
        }

        $container->getDefinition(FileFactory::class)
            ->setArgument('$filesystems', $newFilesystems);

        //
        // autoconfigure services
        //

        $container
            ->registerForAutoconfiguration(FileFilterInterface::class)
            ->addTag('rekalogika.file.derivation.filter');

        $container
            ->registerForAutoconfiguration(PropertyListerInterface::class)
            ->addTag('rekalogika.file.association.property_lister');

        $container
            ->registerForAutoconfiguration(FileLocationResolverInterface::class)
            ->addTag('rekalogika.file.association.file_location_resolver');

        $container
            ->registerForAutoconfiguration(ObjectIdResolverInterface::class)
            ->addTag('rekalogika.file.association.object_id_resolver');
    }
}
