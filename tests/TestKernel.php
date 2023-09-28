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

namespace Rekalogika\File\Tests;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Rekalogika\Contracts\File\FileRepositoryInterface;
use Rekalogika\DirectPropertyAccess\RekalogikaDirectPropertyAccessBundle;
use Rekalogika\File\Association\Contracts\FileLocationResolverInterface;
use Rekalogika\File\Association\Contracts\PropertyListerInterface;
use Rekalogika\File\Association\Contracts\PropertyReaderInterface;
use Rekalogika\File\Association\Contracts\PropertyWriterInterface;
use Rekalogika\File\Association\FileAssociationManager;
use Rekalogika\File\Association\FileLocationResolver\ChainedFileLocationResolver;
use Rekalogika\File\Association\FileLocationResolver\DefaultFileLocationResolver;
use Rekalogika\File\Association\PropertyLister\AttributesPropertyLister;
use Rekalogika\File\Association\PropertyLister\ChainPropertyLister;
use Rekalogika\File\Association\PropertyLister\FileAssociationInterfacePropertyLister;
use Rekalogika\File\Association\PropertyReaderWriter\SymfonyPropertyAccessorBridge;
use Rekalogika\File\Association\Reconstitutor\AttributeReconstitutor;
use Rekalogika\File\Association\Reconstitutor\InterfaceReconstitutor;
use Rekalogika\File\Bundle\RekalogikaFileBundle;
use Rekalogika\File\Image\ImageResizer;
use Rekalogika\Psr16SimpleCacheBundle\RekalogikaPsr16SimpleCacheBundle;
use Rekalogika\Reconstitutor\RekalogikaReconstitutorBundle;
use Rekalogika\TemporaryUrl\RekalogikaTemporaryUrlBundle;
use Rekalogika\TemporaryUrl\TemporaryUrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Kernel as HttpKernelKernel;

class TestKernel extends HttpKernelKernel
{
    /**
     * @param array<string,mixed> $config
     */
    public function __construct(private array $config = [])
    {
        parent::__construct('test', true);
    }

    public function boot(): void
    {
        $filesystem = new Filesystem();
        $filesystem->remove(__DIR__ . '/../var/');

        parent::boot();

    }

    public function registerBundles(): iterable
    {
        yield new FrameworkBundle();
        yield new DoctrineBundle();
        yield new RekalogikaDirectPropertyAccessBundle();
        yield new RekalogikaReconstitutorBundle();
        yield new RekalogikaFileBundle();
        yield new RekalogikaTemporaryUrlBundle();
        yield new RekalogikaPsr16SimpleCacheBundle();
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(function (ContainerBuilder $container) {
            // silence Symfony 6.1 deprecation warning
            $container->loadFromExtension('framework', [
                'http_method_override' => false
            ]);

            $container->loadFromExtension('doctrine', [
                'dbal' => [
                    'driver' => 'pdo_sqlite',
                    'memory' => true,
                    'charset' => 'UTF8',
                ],
            ]);

            $container->loadFromExtension('rekalogika_file', $this->config);
        });
    }

    /**
     * @return iterable<class-string>
     */
    public static function getServiceIds(): iterable
    {
        return [
            FileRepositoryInterface::class,

            ImageResizer::class,

            InterfaceReconstitutor::class,
            AttributeReconstitutor::class,
            FileAssociationManager::class,
            FileLocationResolverInterface::class,
            ChainedFileLocationResolver::class,
            DefaultFileLocationResolver::class,
            PropertyListerInterface::class,
            ChainPropertyLister::class,
            FileAssociationInterfacePropertyLister::class,
            AttributesPropertyLister::class,
            SymfonyPropertyAccessorBridge::class,
            PropertyReaderInterface::class,
            PropertyWriterInterface::class,

            TemporaryUrlGeneratorInterface::class,
        ];
    }
}
