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

namespace Rekalogika\File;

use League\Flysystem\FilesystemOperator;
use League\MimeTypeDetection\MimeTypeDetector;
use Rekalogika\File\Filesystem\RemoteFilesystemDecorator;
use Rekalogika\File\MetadataGenerator\MetadataGenerator;
use Rekalogika\File\MetadataGenerator\MetadataGeneratorInterface;
use Rekalogika\File\MetadataSerializer\MetadataSerializer;
use Rekalogika\File\MetadataSerializer\MetadataSerializerInterface;
use Rekalogika\File\Repository\FileRepository;
use Rekalogika\File\Repository\FilesystemRepository;

class FileFactory
{
    //
    // properties
    //

    protected ?FileRepository $fileRepository = null;

    /**
     * @var array<string,FilesystemOperator>
     */
    protected array $filesystems = [];

    //
    // magic methods
    //

    /**
     * @param iterable<string,FilesystemOperator> $filesystems
     */
    public function __construct(
        iterable $filesystems = [],
        protected ?string $defaultFilesystemIdForTemporaryFile = null,
        protected ?MimeTypeDetector $mimeTypeDetector = null,
    ) {
        $this->filesystems = $filesystems instanceof \Traversable
            ? iterator_to_array($filesystems)
            : $filesystems;
    }

    //
    // factory
    //

    public function getFileRepository(): FileRepository
    {
        if ($this->fileRepository !== null) {
            return $this->fileRepository;
        }

        return $this->fileRepository = new FileRepository(
            $this->getFilesystemRepository(),
            $this->getMetadataGenerator(),
            $this->defaultFilesystemIdForTemporaryFile,
        );
    }

    //
    // dependencies
    //

    public function getFilesystemRepository(): FilesystemRepository
    {
        return new FilesystemRepository(
            $this->getMetadataSidecarFilesystemOperatorDecorator(),
            $this->filesystems,
        );
    }

    protected function getMetadataSidecarFilesystemOperatorDecorator(): RemoteFilesystemDecorator
    {
        return new RemoteFilesystemDecorator(
            $this->getMetadataSerializer(),
            $this->getMetadataGenerator(),
        );
    }

    protected function getMetadataSerializer(): MetadataSerializerInterface
    {
        return new MetadataSerializer();
    }

    protected function getMetadataGenerator(): MetadataGeneratorInterface
    {
        return new MetadataGenerator($this->mimeTypeDetector);
    }

    //
    // setters
    //

    /**
     * @param iterable<string,FilesystemOperator> $filesystems
     */
    public function setFilesystems(iterable $filesystems): self
    {
        $this->filesystems = $filesystems instanceof \Traversable
            ? iterator_to_array($filesystems)
            : $filesystems;

        return $this;
    }

    public function addFilesystem(
        string $identifier,
        FilesystemOperator $filesystem,
    ): self {
        $this->filesystems[$identifier] = $filesystem;

        return $this;
    }

    public function setDefaultFilesystemIdForTemporaryFile(
        ?string $defaultFilesystemIdForTemporaryFile,
    ): self {
        $this->defaultFilesystemIdForTemporaryFile = $defaultFilesystemIdForTemporaryFile;

        return $this;
    }

    public function setMimeTypeDetector(?MimeTypeDetector $mimeTypeDetector): self
    {
        $this->mimeTypeDetector = $mimeTypeDetector;

        return $this;
    }
}
