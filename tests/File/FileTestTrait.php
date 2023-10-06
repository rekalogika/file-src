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

namespace Rekalogika\File\Tests\File;

use Rekalogika\Contracts\File\FileInterface;
use Rekalogika\Contracts\File\FileMetadataInterface;
use Rekalogika\Contracts\File\Metadata\ImageMetadataInterface;
use Rekalogika\Contracts\File\RawMetadataInterface;
use Rekalogika\Domain\File\Metadata\Constants;

trait FileTestTrait
{
    protected function assertFileInterface(
        FileInterface $file,
        ?string $filesystemIdentifier,
        string $key,
        string $content,
        string $type,
        ?int $width = null,
        ?int $height = null,
        ?string $fileName = null,
    ): void {
        $testDir = __DIR__ . '/../../var/test';
        $localTestFile = $testDir . '/localfiletest.txt';
        @mkdir($testDir, recursive: true);
        @unlink($localTestFile);

        $this->assertSame($file->getFilesystemIdentifier(), $filesystemIdentifier);
        $this->assertSame($file->getKey(), $key);

        // pointer tests
        $pointer = $file->getPointer();
        $this->assertTrue($file->isEqualTo($pointer));
        $this->assertTrue($pointer->isEqualTo($file));
        $this->assertTrue($file->isSameFilesystem($pointer));
        $this->assertTrue($pointer->isSameFilesystem($file));
        $this->assertSame($file->getKey(), $pointer->getKey());
        $this->assertSame($file->getFilesystemIdentifier(), $pointer->getFilesystemIdentifier());

        // filename tests
        if ($fileName !== null) {
            $fullName = \pathinfo($fileName, PATHINFO_BASENAME);
            $extension = \pathinfo($fileName, PATHINFO_EXTENSION);
            $fileNameWithoutExtension = \pathinfo($fileName, PATHINFO_FILENAME);

            $this->assertSame((string) $file->getName()->getFull(), $fullName);
            $this->assertSame((string) $file->getName()->getBase(), $fileNameWithoutExtension);
            $this->assertSame($file->getName()->getExtension() ?? '', $extension);
            $this->assertSame($file->getName()->hasExtension(), !empty($extension));
        }

        // content tests
        $this->assertSame($file->getContentAsStream()->getContents(), $content);
        $this->assertSame($file->getContent(), $content);

        // local file saving tests
        $splFileInfo = $file->saveToLocalFile($localTestFile);
        $this->assertSame($splFileInfo->getRealPath(), \realpath($localTestFile));
        $this->assertSame(\file_get_contents($localTestFile), $content);
        unlink($localTestFile);

        // local temporary file tests
        $localTemporaryFile = $file->createLocalTemporaryFile();
        $this->assertSame(\file_get_contents($localTemporaryFile->getRealPath()), $content);
        $localTemporaryFilePath = $localTemporaryFile->getRealPath();
        unset($localTemporaryFile);
        $this->assertFileDoesNotExist($localTemporaryFilePath);

        // media type tests
        $this->assertSame($file->getType()->getName(), $type);
        $this->assertSame($file->getType()->getType(), \explode('/', $type)[0]);
        $this->assertSame($file->getType()->getSubType(), \explode('/', $type)[1]);

        // size tests
        $this->assertSame($file->getSize(), \strlen($content));

        // last modified
        $this->assertInstanceOf(\DateTimeInterface::class, $file->getLastModified());

        // metadata tests
        $metadata = $file->get(RawMetadataInterface::class);
        $this->assertNotNull($metadata);

        $this->assertSame($metadata->get(Constants::FILE_SIZE), \strlen($content));
        $this->assertSame($file->get(FileMetadataInterface::class)?->getSize(), \strlen($content));

        $this->assertSame($metadata->get(Constants::FILE_TYPE), $type);

        $fileMetadata =  $file->get(FileMetadataInterface::class);
        $this->assertNotNull($fileMetadata);
        $this->assertSame((string) $fileMetadata->getType(), $type);

        $metadataLastModified = $metadata->get(Constants::FILE_MODIFICATION_TIME);
        $this->assertTrue(is_int($metadataLastModified));
        $this->assertSame(
            $fileMetadata->getModificationTime()->getTimestamp(),
            $metadata->get(Constants::FILE_MODIFICATION_TIME)
        );

        // width and height tests
        if ($width !== null && $height !== null) {
            $metaHeight = $metadata->get(Constants::MEDIA_HEIGHT);
            $metaWidth = $metadata->get(Constants::MEDIA_WIDTH);

            $this->assertSame($metaHeight, $height);
            $this->assertSame($metaWidth, $width);

            $imageMetadata = $file->get(ImageMetadataInterface::class);
            $this->assertNotNull($imageMetadata);

            $this->assertSame($imageMetadata->getHeight(), $height);
            $this->assertSame($imageMetadata->getWidth(), $width);
        }
    }
}
