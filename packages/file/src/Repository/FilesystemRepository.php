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

namespace Rekalogika\File\Repository;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemOperator;
use League\MimeTypeDetection\FinfoMimeTypeDetector;
use Rekalogika\File\Contracts\FilesystemRepositoryInterface;
use Rekalogika\File\Contracts\MetadataAwareFilesystemOperator;
use Rekalogika\File\Exception\FilesystemRepository\FilesystemAlreadyExistsException;
use Rekalogika\File\Exception\FilesystemRepository\FilesystemNotFoundException;
use Rekalogika\File\Filesystem\LocalFilesystemDecorator;
use Rekalogika\File\Filesystem\RemoteFilesystemDecorator;

class FilesystemRepository implements FilesystemRepositoryInterface
{
    private static ?MetadataAwareFilesystemOperator $localFilesystem = null;

    /**
     * @var array<string,MetadataAwareFilesystemOperator>
     */
    private array $filesystems = [];

    /**
     * @param iterable<string,FilesystemOperator> $filesystems
     */
    public function __construct(
        private RemoteFilesystemDecorator $metadataSidecarFilesystemOperatorDecorator,
        iterable $filesystems = [],
    ) {
        foreach ($filesystems as $identifier => $filesystem) {
            $this->addFilesystem($identifier, $filesystem);
        }
    }

    public static function getLocalFilesystem(): MetadataAwareFilesystemOperator
    {
        if (null !== self::$localFilesystem) {
            return self::$localFilesystem;
        }

        $mimeTypeDetector = new FinfoMimeTypeDetector();

        return self::$localFilesystem = new LocalFilesystemDecorator(
            new Filesystem(
                new LocalFilesystemAdapter(
                    '/',
                    mimeTypeDetector: $mimeTypeDetector
                )
            )
        );
    }

    public function addFilesystem(
        string $identifier,
        FilesystemOperator $filesystem
    ): void {
        if (isset($this->filesystems[$identifier])) {
            throw new FilesystemAlreadyExistsException($identifier);
        }

        if (!$filesystem instanceof MetadataAwareFilesystemOperator) {
            $filesystem = $this->metadataSidecarFilesystemOperatorDecorator
                ->withFilesystem($filesystem);
        }

        $this->filesystems[$identifier] = $filesystem;
    }

    public function getFilesystem(
        ?string $identifier
    ): MetadataAwareFilesystemOperator {
        if (null === $identifier) {
            return self::getLocalFilesystem();
        }

        if (!isset($this->filesystems[$identifier])) {
            throw new FilesystemNotFoundException($identifier);
        }

        return $this->filesystems[$identifier];
    }
}
