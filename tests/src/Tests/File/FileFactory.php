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

namespace Rekalogika\File\Tests\Tests\File;

use League\Flysystem\Filesystem;
use League\Flysystem\InMemory\InMemoryFilesystemAdapter;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\MimeTypeDetection\FinfoMimeTypeDetector;
use Rekalogika\Contracts\File\FileRepositoryInterface;
use Rekalogika\File\Filesystem\RemoteFilesystemDecorator;
use Rekalogika\File\MetadataGenerator\MetadataGenerator;
use Rekalogika\File\MetadataGenerator\MetadataGeneratorInterface;
use Rekalogika\File\MetadataSerializer\MetadataSerializer;
use Rekalogika\File\Repository\FileRepository;
use Rekalogika\File\Repository\FilesystemRepository;

class FileFactory
{
    private function __construct() {}

    private static ?MetadataGeneratorInterface $metadataGenerator = null;

    public static function createFilesystemRepository(): FilesystemRepository
    {
        $metadataSerializer = new MetadataSerializer();
        $metadataSidecarDecorator = new RemoteFilesystemDecorator(
            $metadataSerializer,
            self::createMetadataGenerator(),
        );

        return new FilesystemRepository($metadataSidecarDecorator);
    }

    public static function createMetadataGenerator(): MetadataGeneratorInterface
    {
        if (self::$metadataGenerator !== null) {
            return self::$metadataGenerator;
        }

        $mimeTypeDetector = new FinfoMimeTypeDetector();

        return self::$metadataGenerator = new MetadataGenerator($mimeTypeDetector);
    }

    public static function createFilesystemRepositoryWithDefaultFilesystems(): FilesystemRepository
    {
        $repository = self::createFilesystemRepository();

        // local filesystem
        $repository->addFilesystem(
            'local',
            new Filesystem(
                new LocalFilesystemAdapter(__DIR__ . '/../../var/test'),
            ),
        );

        // in memory filesystem
        $repository->addFilesystem(
            'inmemory',
            new Filesystem(new InMemoryFilesystemAdapter()),
        );

        // another in memory filesystem
        $repository->addFilesystem(
            'inmemory2',
            new Filesystem(new InMemoryFilesystemAdapter()),
        );

        return $repository;
    }

    public static function createFileRepository(): FileRepositoryInterface
    {
        $filesystemRepository = self::createFilesystemRepositoryWithDefaultFilesystems();

        return new FileRepository(
            $filesystemRepository,
            self::createMetadataGenerator(),
        );
    }
}
