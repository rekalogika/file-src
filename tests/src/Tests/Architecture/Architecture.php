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

namespace Rekalogika\File\Tests\Tests\Architecture;

use Doctrine\ORM\Mapping\Embedded;
use Doctrine\Persistence\ManagerRegistry;
use PHPat\Selector\Selector;
use PHPat\Selector\SelectorInterface;
use PHPat\Test\Builder\Rule;
use PHPat\Test\PHPat;
use Rekalogika\File\Association\Attribute\AsFileAssociation;
use Rekalogika\File\Association\Attribute\WithFileAssociation;
use Rekalogika\File\Association\Model\FetchMode;
use Rekalogika\File\File;
use Rekalogika\File\FileAdapter;
use Rekalogika\File\FileFactory;
use Rekalogika\File\RawMetadata;
use Rekalogika\File\TemporaryFile;

final class Architecture
{
    //
    // selectors for our packages
    //

    private function getFile(): SelectorInterface
    {
        return Selector::AllOf(
            Selector::inNamespace('Rekalogika\File'),
            Selector::not(Selector::inNamespace('Rekalogika\File\Bridge')),
            Selector::not(Selector::inNamespace('Rekalogika\File\Tests')),
            Selector::not($this->getFileAssociation()),
            Selector::not($this->getFileBundle()),
            Selector::not($this->getFileDerivation()),
            Selector::not($this->getFileImage()),
            Selector::not($this->getFileServer()),
            Selector::not($this->getFileZip()),
        );
    }

    private function getFileAssociation(): SelectorInterface
    {
        return Selector::inNamespace('Rekalogika\File\Association');
    }

    private function getFileAssociationContracts(): SelectorInterface
    {
        return Selector::inNamespace('Rekalogika\Contracts\File\Association');
    }

    private function getFileAssociationEntity(): SelectorInterface
    {
        return Selector::inNamespace('Rekalogika\Domain\File\Association\Entity');
    }

    private function getFileBundle(): SelectorInterface
    {
        return Selector::inNamespace('Rekalogika\File\Bundle');
    }

    private function getFileContracts(): SelectorInterface
    {
        return Selector::AllOf(
            Selector::inNamespace('Rekalogika\Contracts\File'),
            Selector::not($this->getFileAssociationContracts()),
            Selector::not($this->getFileMetadataContracts()),
        );
    }

    private function getFileDerivation(): SelectorInterface
    {
        return Selector::inNamespace('Rekalogika\File\Derivation');
    }

    private function getFileFilepond(): SelectorInterface
    {
        return Selector::inNamespace('Rekalogika\File\Bridge\FilePond');
    }

    private function getFileImage(): SelectorInterface
    {
        return Selector::inNamespace('Rekalogika\File\Image');
    }

    private function getFileMetadata(): SelectorInterface
    {
        return Selector::inNamespace('Rekalogika\Domain\File\Metadata');
    }

    private function getFileMetadataContracts(): SelectorInterface
    {
        return Selector::inNamespace('Rekalogika\Contracts\File\Metadata');
    }

    private function getFileNull(): SelectorInterface
    {
        return Selector::inNamespace('Rekalogika\Domain\File\Null');
    }

    private function getFileOneupUploaderBridge(): SelectorInterface
    {
        return Selector::inNamespace('Rekalogika\File\Bridge\OneupUploader');
    }

    private function getFileServer(): SelectorInterface
    {
        return Selector::inNamespace('Rekalogika\File\Server');
    }

    private function getFileSymfonyBridge(): SelectorInterface
    {
        return Selector::inNamespace('Rekalogika\File\Bridge\Symfony');
    }

    private function getFileZip(): SelectorInterface
    {
        return Selector::inNamespace('Rekalogika\File\Zip');
    }

    //
    // rules
    //

    public function testFile(): Rule
    {
        return PHPat::rule()
            ->classes($this->getFile())
            ->canOnlyDependOn()
            ->classes(
                // self
                $this->getFile(),

                // dependencies on our packages
                $this->getFileContracts(),
                $this->getFileMetadata(),

                // external dependencies
                Selector::inNamespace('Http\Discovery'),
                Selector::inNamespace('League\Flysystem'),
                Selector::inNamespace('League\MimeTypeDetection'),
                Selector::inNamespace('Psr\Http\Message'),

                // dependencies on optional packages
                $this->getFileSymfonyBridge(),
                $this->getFileOneupUploaderBridge(),
                Selector::inNamespace('Symfony\Component\HttpFoundation'),
                Selector::inNamespace('Oneup\UploaderBundle'),

                // soft dependencies
                Selector::classname(\Override::class),

                // internal PHP classes
                Selector::classname(\IteratorAggregate::class),
                Selector::classname(\Traversable::class),
                Selector::classname(\DateTimeInterface::class),
                Selector::classname(\DateTimeImmutable::class),
                Selector::classname(\SplFileInfo::class),

                // exceptions
                Selector::classname(\RuntimeException::class),
                Selector::classname(\LogicException::class),
                Selector::classname(\InvalidArgumentException::class),
            );
    }

    public function testFileAssociation(): Rule
    {
        return PHPat::rule()
            ->classes($this->getFileAssociation())
            ->canOnlyDependOn()
            ->classes(
                // self
                $this->getFileAssociation(),

                // dependencies on our packages
                $this->getFileContracts(),
                $this->getFileNull(),
                $this->getFileAssociationContracts(),

                // external dependencies
                Selector::inNamespace('Psr\Http\Message'),
                Selector::inNamespace('Psr\Log'),
                Selector::inNamespace('Psr\Cache'),
                Selector::inNamespace('Rekalogika\Reconstitutor\Contract'),

                // dependencies on optional packages
                Selector::classname(ManagerRegistry::class),

                // soft dependencies
                Selector::classname(\Override::class),
                Selector::classname(\Attribute::class),

                // internal PHP classes
                Selector::classname(\SplFileInfo::class),
                Selector::classname(\DateTimeInterface::class),
                Selector::classname(\DateTimeImmutable::class),
                Selector::classname(\WeakMap::class),
                Selector::classname(\UnitEnum::class),

                // reflection
                Selector::classname(\ReflectionClass::class),
                Selector::classname(\ReflectionProperty::class),

                // exceptions
                Selector::classname(\Throwable::class),
                Selector::classname(\RuntimeException::class),
                Selector::classname(\LogicException::class),
                Selector::classname(\UnexpectedValueException::class),
                Selector::classname(\InvalidArgumentException::class),
            );
    }

    public function testFileAssociationContracts(): Rule
    {
        return PHPat::rule()
            ->classes($this->getFileAssociationContracts())
            ->canOnlyDependOn()
            ->classes(
                // self
                $this->getFileAssociationContracts(),
            );
    }

    public function testFileAssociationEntity(): Rule
    {
        return PHPat::rule()
            ->classes($this->getFileAssociationEntity())
            ->canOnlyDependOn()
            ->classes(
                // self
                $this->getFileAssociationEntity(),

                // dependencies on our packages
                $this->getFileContracts(),
                $this->getFileNull(),
                $this->getFileMetadata(),

                // external dependencies
                Selector::inNamespace('Psr\Http\Message'),
                Selector::inNamespace('Symfony\Contracts\Translation'),
                Selector::inNamespace('Rekalogika\Collections\Decorator'),
                Selector::inNamespace('Doctrine\Common\Collections'),

                // soft dependencies (attributes)
                Selector::classname(Embedded::class),
                Selector::classname(\Override::class),
                Selector::classname(AsFileAssociation::class),
                Selector::classname(WithFileAssociation::class),
                Selector::classname(FetchMode::class),

                // internal PHP classes
                Selector::classname(\SplFileInfo::class),
                Selector::classname(\DateTimeInterface::class),
                Selector::classname(\DateTimeImmutable::class),
                Selector::classname(\Stringable::class),
                Selector::classname(\IteratorAggregate::class),
                Selector::classname(\Traversable::class),

                // exceptions
                Selector::classname(\RuntimeException::class),
                Selector::classname(\InvalidArgumentException::class),

                // @todo replace
                Selector::classname(RawMetadata::class),
            );
    }

    public function testFileBundle(): Rule
    {
        return PHPat::rule()
            ->classes($this->getFileBundle())
            ->canOnlyDependOn()
            ->classes(
                // self
                $this->getFileBundle(),

                // dependencies on our packages
                $this->getFileContracts(),
                $this->getFileAssociationContracts(),
                $this->getFileAssociationEntity(),

                // dependencies on our optional packages
                $this->getFileAssociation(),
                $this->getFileDerivation(),
                $this->getFileImage(),
                $this->getFileServer(),
                $this->getFileZip(),

                // external dependencies
                Selector::inNamespace('League\Flysystem'),
                Selector::inNamespace('Symfony\Component\Config'),
                Selector::inNamespace('Symfony\Component\Console'),
                Selector::inNamespace('Symfony\Component\DependencyInjection'),
                Selector::inNamespace('Symfony\Component\HttpKernel'),

                // soft dependencies (attributes)
                Selector::classname(\Override::class),

                // reflection
                Selector::classname(\ReflectionClass::class),

                // exceptions
                Selector::classname(\RuntimeException::class),
                Selector::classname(\InvalidArgumentException::class),

                // tests
                Selector::inNamespace('Rekalogika\File\Tests'),

                // @todo replace
                Selector::classname(FileFactory::class),
            );
    }

    public function testFileContracts(): Rule
    {
        return PHPat::rule()
            ->classes($this->getFileContracts())
            ->canOnlyDependOn()
            ->classes(
                // self
                $this->getFileContracts(),

                // external dependencies
                Selector::inNamespace('Symfony\Contracts\Translation'),
                Selector::inNamespace('Psr\Http\Message'),

                // soft dependencies
                Selector::classname(\Override::class),

                // internal PHP classes
                Selector::classname(\Stringable::class),
                Selector::classname(\Countable::class),
                Selector::classname(\Traversable::class),
                Selector::classname(\SplFileInfo::class),
                Selector::classname(\DateTimeInterface::class),

                // exceptions
                Selector::classname(\Throwable::class),
                Selector::classname(\RuntimeException::class),
                Selector::classname(\LogicException::class),
            );
    }

    public function testFileDerivation(): Rule
    {
        return PHPat::rule()
            ->classes($this->getFileDerivation())
            ->canOnlyDependOn()
            ->classes(
                // self
                $this->getFileDerivation(),

                // dependencies on our packages
                $this->getFile(),
                $this->getFileContracts(),

                // soft dependencies
                Selector::classname(\Override::class),

                // exceptions
                Selector::classname(\LogicException::class),
            );
    }

    public function testFileFilepond(): Rule
    {
        return PHPat::rule()
            ->classes($this->getFileFilepond())
            ->canOnlyDependOn()
            ->classes(
                // self
                $this->getFileFilepond(),

                // dependencies on our packages
                $this->getFileContracts(),
                $this->getFileSymfonyBridge(),

                // external dependencies
                Selector::inNamespace('Symfony\Component\DependencyInjection'),
                Selector::inNamespace('Symfony\Component\Form'),
                Selector::inNamespace('Symfony\Component\HttpFoundation'),
                Selector::inNamespace('Symfony\Component\HttpKernel'),
                Selector::inNamespace('Symfony\Component\OptionsResolver'),

                // optional dependencies
                Selector::inNamespace('Symfony\Component\AssetMapper'),

                // soft dependencies (attributes)
                Selector::classname(\Override::class),

                // internal PHP classes
                Selector::classname(\JsonException::class),

                // exceptions
                Selector::classname(\InvalidArgumentException::class),
                Selector::classname(\UnexpectedValueException::class),

                // @todo replace
                Selector::classname(TemporaryFile::class),
            );
    }

    public function testFileImage(): Rule
    {
        return PHPat::rule()
            ->classes($this->getFileImage())
            ->canOnlyDependOn()
            ->classes(
                // self
                $this->getFileImage(),

                // dependencies on our packages
                $this->getFileContracts(),
                $this->getFileDerivation(),

                // external dependencies
                Selector::inNamespace('Intervention\Image'),
                Selector::inNamespace('Psr\Container'),
                Selector::inNamespace('Psr\Log'),
                Selector::inNamespace('Symfony\Contracts\Service'),
                Selector::inNamespace('Twig'),

                // soft dependencies
                Selector::classname(\Override::class),

                // exceptions
                Selector::classname(\Throwable::class),
            );
    }

    public function testFileMetadata(): Rule
    {
        return PHPat::rule()
            ->classes($this->getFileMetadata())
            ->canOnlyDependOn()
            ->classes(
                // self
                $this->getFileMetadata(),

                // dependencies on our packages
                $this->getFileContracts(),
                $this->getFileMetadataContracts(),

                // external dependencies
                Selector::inNamespace('cardinalby\ContentDisposition'),
                Selector::inNamespace('FileEye\MimeMap'),
                Selector::inNamespace('Symfony\Contracts\Translation'),

                // soft dependencies
                Selector::classname(\Override::class),

                // internal PHP classes
                Selector::classname(\Stringable::class),
                Selector::classname(\DateTimeImmutable::class),
                Selector::classname(\DateTimeInterface::class),
                Selector::classname(\DateTimeZone::class),

                // exceptions
                Selector::classname(\BadMethodCallException::class),
                Selector::classname(\InvalidArgumentException::class),
            );
    }

    public function testFileMetadataContracts(): Rule
    {
        return PHPat::rule()
            ->classes($this->getFileMetadataContracts())
            ->canOnlyDependOn()
            ->classes(
                // self
                $this->getFileMetadataContracts(),
            );
    }

    public function testFileNull(): Rule
    {
        return PHPat::rule()
            ->classes($this->getFileNull())
            ->canOnlyDependOn()
            ->classes(
                // self
                $this->getFileNull(),

                // dependencies on our packages
                $this->getFileContracts(),

                // external dependencies
                Selector::inNamespace('Symfony\Contracts\Translation'),
                Selector::inNamespace('Psr\Http\Message'),

                // soft dependencies
                Selector::classname(\Override::class),

                // internal PHP classes
                Selector::classname(\Stringable::class),
                Selector::classname(\SplFileInfo::class),
                Selector::classname(\DateTimeInterface::class),
                Selector::classname(\DateTimeImmutable::class),

                // exceptions
                Selector::classname(\RuntimeException::class),
            );
    }

    public function testFileOneupUploaderBridge(): Rule
    {
        return PHPat::rule()
            ->classes($this->getFileOneupUploaderBridge())
            ->canOnlyDependOn()
            ->classes(
                // self
                $this->getFileOneupUploaderBridge(),

                // dependencies on our packages
                $this->getFileContracts(),

                // external dependencies
                Selector::inNamespace('Oneup\UploaderBundle'),
                Selector::inNamespace('League\Flysystem'),

                // exceptions
                Selector::classname(\InvalidArgumentException::class),

                // @todo remove
                Selector::classname(File::class),
            );
    }

    public function testFileServer(): Rule
    {
        return PHPat::rule()
            ->classes($this->getFileServer())
            ->canOnlyDependOn()
            ->classes(
                // self
                $this->getFileServer(),

                // dependencies on our packages
                $this->getFileContracts(),
                $this->getFileSymfonyBridge(),

                // external dependencies
                Selector::inNamespace('Rekalogika\TemporaryUrl'),
                Selector::inNamespace('Symfony\Component\HttpFoundation'),
            );
    }

    public function testFileSymfonyBridge(): Rule
    {
        return PHPat::rule()
            ->classes($this->getFileSymfonyBridge())
            ->canOnlyDependOn()
            ->classes(
                // self
                $this->getFileSymfonyBridge(),

                // dependencies on our packages
                $this->getFileContracts(),
                $this->getFileMetadata(), // consider trying to remove this
                $this->getFileMetadataContracts(),

                // external dependencies
                Selector::inNamespace('Symfony\Component\Form'),
                Selector::inNamespace('Symfony\Component\HttpFoundation'),
                Selector::inNamespace('Symfony\Component\Validator'),

                // soft dependencies
                Selector::classname(\Override::class),
                Selector::classname(\Attribute::class),

                // internal PHP classes
                Selector::classname(\SplFileInfo::class),
                Selector::classname(\SplFileObject::class),

                // exceptions
                Selector::classname(\RuntimeException::class),

                // @todo remove
                Selector::classname(File::class),
                Selector::classname(RawMetadata::class),
                Selector::classname(FileAdapter::class),
            );
    }

    public function testFileZip(): Rule
    {
        return PHPat::rule()
            ->classes($this->getFileZip())
            ->canOnlyDependOn()
            ->classes(
                // self
                $this->getFileZip(),

                // dependencies on our packages
                $this->getFileContracts(),
                $this->getFileMetadata(),

                // external dependencies
                Selector::inNamespace('Psr\Http\Message'),
                Selector::inNamespace('Rekalogika\TemporaryUrl'),
                Selector::inNamespace('Symfony\Contracts\Translation'),
                Selector::inNamespace('Symfony\Component\HttpFoundation'),
                Selector::inNamespace('ZipStream'),

                // soft dependencies
                Selector::classname(\Override::class),

                // internal PHP classes
                Selector::classname(\Stringable::class),
                Selector::classname(\IteratorAggregate::class),
                Selector::classname(\Traversable::class),
            );
    }
}
