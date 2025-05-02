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
use League\FlysystemBundle\FlysystemBundle;
use Rekalogika\Contracts\File\FileRepositoryInterface;
use Rekalogika\DirectPropertyAccess\RekalogikaDirectPropertyAccessBundle;
use Rekalogika\File\Association\Contracts\ClassMetadataFactoryInterface;
use Rekalogika\File\Association\Contracts\PropertyListerInterface;
use Rekalogika\File\Association\Contracts\PropertyReaderInterface;
use Rekalogika\File\Association\Contracts\PropertyWriterInterface;
use Rekalogika\File\Association\FileAssociationManager;
use Rekalogika\File\Association\PropertyLister\AttributesPropertyLister;
use Rekalogika\File\Association\PropertyLister\ChainPropertyLister;
use Rekalogika\File\Association\PropertyLister\FileAssociationInterfacePropertyLister;
use Rekalogika\File\Association\PropertyReaderWriter\SymfonyPropertyAccessorBridge;
use Rekalogika\File\Association\Reconstitutor\AttributeReconstitutor;
use Rekalogika\File\Association\Reconstitutor\InterfaceReconstitutor;
use Rekalogika\File\Bridge\FilePond\RekalogikaFileFilePondBundle;
use Rekalogika\File\Bundle\RekalogikaFileBundle;
use Rekalogika\File\Image\ImageResizer;
use Rekalogika\File\Zip\FileZip;
use Rekalogika\File\Zip\ZipDirectory;
use Rekalogika\Reconstitutor\RekalogikaReconstitutorBundle;
use Rekalogika\TemporaryUrl\RekalogikaTemporaryUrlBundle;
use Rekalogika\TemporaryUrl\TemporaryUrlGeneratorInterface;
use Symfony\Bundle\DebugBundle\DebugBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\MakerBundle\MakerBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Bundle\WebProfilerBundle\WebProfilerBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as HttpKernelKernel;
use Symfony\UX\StimulusBundle\StimulusBundle;
use Symfony\UX\Turbo\TurboBundle;
use Twig\Extra\TwigExtraBundle\TwigExtraBundle;

final class TestKernel extends HttpKernelKernel
{
    use MicroKernelTrait {
        registerContainerConfiguration as private baseRegisterContainerConfiguration;
    }
    /**
     * @param array<string,mixed> $config
     */
    public function __construct(
        private string $env = 'test',
        bool $debug = true,
        private readonly array $config = [],
    ) {
        parent::__construct($env, $debug);
    }

    #[\Override]
    public function boot(): void
    {
        parent::boot();
    }

    #[\Override]
    public function registerBundles(): iterable
    {
        yield new FrameworkBundle();
        yield new DoctrineBundle();
        yield new DebugBundle();
        yield new WebProfilerBundle();
        yield new TwigBundle();
        yield new MakerBundle();
        yield new StimulusBundle();
        yield new TurboBundle();
        yield new TwigExtraBundle();
        yield new RekalogikaDirectPropertyAccessBundle();
        yield new RekalogikaReconstitutorBundle();
        yield new RekalogikaFileBundle();
        yield new RekalogikaFileFilePondBundle();
        yield new RekalogikaTemporaryUrlBundle();
        yield new FlysystemBundle();
    }

    #[\Override]
    protected function build(ContainerBuilder $container): void
    {
        parent::build($container);

    }

    #[\Override]
    public function getBuildDir(): string
    {
        return $this->getProjectDir() . '/var/build/' . $this->env;
    }

    #[\Override]
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $this->baseRegisterContainerConfiguration($loader);

        $loader->load(function (ContainerBuilder $container): void {
            $container->loadFromExtension('rekalogika_file', $this->config);
        });
    }

    #[\Override]
    public function getProjectDir(): string
    {
        return __DIR__ . '/../';
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
            PropertyListerInterface::class,
            ChainPropertyLister::class,
            FileAssociationInterfacePropertyLister::class,
            AttributesPropertyLister::class,
            SymfonyPropertyAccessorBridge::class,
            PropertyReaderInterface::class,
            PropertyWriterInterface::class,
            ClassMetadataFactoryInterface::class,

            TemporaryUrlGeneratorInterface::class,

            FileZip::class,
            ZipDirectory::class,
        ];
    }
}
