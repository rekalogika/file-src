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

use Rekalogika\Domain\File\Association\Entity\EmbeddedMetadata;
use Rekalogika\File\Association\Contracts\ClassBasedFileLocationResolverInterface;
use Rekalogika\File\Association\Contracts\ClassSignatureResolverInterface;
use Rekalogika\File\Association\Contracts\ObjectIdResolverInterface;
use Rekalogika\File\Association\Contracts\ObjectManagerInterface;
use Rekalogika\File\Association\Contracts\PropertyListerInterface;
use Rekalogika\File\Bundle\DefaultFilesystemFactory;
use Rekalogika\File\Derivation\Filter\FileFilterInterface;
use Rekalogika\File\Image\ImageResizer;
use Rekalogika\File\Server\FileInterfaceResourceServer;
use Rekalogika\File\Tests\TestKernel;
use Rekalogika\File\Zip\FileZip;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Stopwatch\Stopwatch;

final class RekalogikaFileExtension extends Extension implements PrependExtensionInterface
{
    #[\Override]
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new PhpFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../../config'),
        );

        $debug = (bool) $container->getParameter('kernel.debug');

        //
        // load service configurations
        //

        $loader->load('file.php');
        $loader->load('file-bundle.php');

        if (interface_exists(ObjectManagerInterface::class)) {
            $loader->load('file-association.php');

            if ($debug && class_exists(Stopwatch::class)) {
                $loader->load('file-association-debug.php');
            }
        }

        if (class_exists(ImageResizer::class)) {
            $loader->load('file-image.php');
        }

        if (class_exists(FileInterfaceResourceServer::class)) {
            $loader->load('file-server.php');
        }

        if (class_exists(FileZip::class)) {
            $loader->load('file-zip.php');
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

        if (\count($filesystems) === 0) {
            $filesystems = [
                'default' => 'rekalogika.file.default_filesystem',
            ];
        }

        $newFilesystems = [];

        foreach ($filesystems as $name => $serviceId) {
            $newFilesystems[$name] = new Reference($serviceId);
        }

        $container
            ->getDefinition('rekalogika.file.factory')
            ->setArgument('$filesystems', $newFilesystems);

        //
        // autoconfigure services
        //

        if (interface_exists(FileFilterInterface::class)) {
            $container
                ->registerForAutoconfiguration(FileFilterInterface::class)
                ->addTag('rekalogika.file.derivation.filter');
        }

        $container
            ->registerForAutoconfiguration(PropertyListerInterface::class)
            ->addTag('rekalogika.file.association.property_lister');

        $container
            ->registerForAutoconfiguration(ClassBasedFileLocationResolverInterface::class)
            ->addTag('rekalogika.file.association.class_based_file_location_resolver');

        $container
            ->registerForAutoconfiguration(ClassSignatureResolverInterface::class)
            ->addTag('rekalogika.file.association.class_signature_resolver');

        $container
            ->registerForAutoconfiguration(ObjectIdResolverInterface::class)
            ->addTag('rekalogika.file.association.object_id_resolver');
    }

    #[\Override]
    public function prepend(ContainerBuilder $container): void
    {
        if (
            class_exists(EmbeddedMetadata::class)
        ) {
            $path = (new \ReflectionClass(EmbeddedMetadata::class))
                ->getFileName();

            if ($path === false) {
                throw new \RuntimeException('Unable to get path of EmbeddedMetadata class');
            }

            $configDir = realpath(\dirname($path, 2) . '/config/doctrine');
            if (false === $configDir) {
                throw new \RuntimeException('Unable to get path of EmbeddedMetadata class');
            }

            $container->loadFromExtension('doctrine', [
                'orm' => [
                    'mappings' => [
                        'rekalogika_file' => [
                            'type' => 'xml',
                            'dir' => $configDir,
                            'prefix' => 'Rekalogika\Domain\File\Association\Entity',
                            'is_bundle' => false,
                            'alias' => 'RekalogikaFile',
                        ],
                    ],
                ],
            ]);
        }
    }
}
