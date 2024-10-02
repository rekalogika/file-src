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

namespace Rekalogika\File\Tests\Tests\FileAssociationEntity;

use PHPUnit\Framework\TestCase;
use Rekalogika\Contracts\File\RawMetadataInterface;
use Rekalogika\Domain\File\Association\Entity\FileDecorator;
use Rekalogika\Domain\File\Metadata\Constants;
use Rekalogika\File\TemporaryFile;
use Rekalogika\File\Tests\Tests\File\FileTestTrait;
use Rekalogika\File\Tests\Tests\Model\EntityWithEmbeddedMetadata;

class EmbeddedMetadataTest extends TestCase
{
    use FileTestTrait;

    public function testEmbeddedMetadata(): void
    {
        $entity = new EntityWithEmbeddedMetadata('foo');

        $file = TemporaryFile::create('test');
        $file->setContent('test-temporary-file');

        $path = $file->getKey();

        $this->assertFileInterface(
            file: $file,
            filesystemIdentifier: null,
            key: $path,
            fileName: 'Untitled.txt',
            content: 'test-temporary-file',
            type: 'text/plain',
        );

        $entity->setFile($file);

        $file2 = $entity->getFile();
        $this->assertInstanceOf(FileDecorator::class, $file2);

        $this->assertFileInterface(
            file: $file2,
            filesystemIdentifier: null,
            key: $path,
            fileName: 'Untitled.txt',
            content: 'test-temporary-file',
            type: 'text/plain',
        );


        $originalMetadata = $file->get(RawMetadataInterface::class);
        $this->assertInstanceOf(RawMetadataInterface::class, $originalMetadata);
        $entityMetadata = $entity->getFileMetadata();
        $this->assertInstanceOf(RawMetadataInterface::class, $entityMetadata);

        $this->assertSame(
            $originalMetadata->get(Constants::FILE_NAME),
            $entityMetadata->get(Constants::FILE_NAME),
        );
        $this->assertSame(
            $originalMetadata->get(Constants::FILE_SIZE),
            $entityMetadata->get(Constants::FILE_SIZE),
        );
        $this->assertSame(
            $originalMetadata->get(Constants::FILE_TYPE),
            $entityMetadata->get(Constants::FILE_TYPE),
        );
        $this->assertSame(
            $originalMetadata->get(Constants::FILE_MODIFICATION_TIME),
            $entityMetadata->get(Constants::FILE_MODIFICATION_TIME),
        );
        $this->assertSame(
            $originalMetadata->tryGet(Constants::MEDIA_WIDTH),
            $entityMetadata->tryGet(Constants::MEDIA_WIDTH),
        );
        $this->assertSame(
            $originalMetadata->tryGet(Constants::MEDIA_HEIGHT),
            $entityMetadata->tryGet(Constants::MEDIA_HEIGHT),
        );

        // setting high-level name

        $file2->setName('foo.txt');

        $this->assertFileInterface(
            file: $file,
            filesystemIdentifier: null,
            key: $path,
            fileName: 'foo.txt',
            content: 'test-temporary-file',
            type: 'text/plain',
        );

        $this->assertFileInterface(
            file: $file2,
            filesystemIdentifier: null,
            key: $path,
            fileName: 'foo.txt',
            content: 'test-temporary-file',
            type: 'text/plain',
        );

        $this->assertSame(
            $originalMetadata->get(Constants::FILE_NAME),
            $entityMetadata->get(Constants::FILE_NAME),
        );

        // setting high-level type

        $file2->setType('application/octet-stream');

        $this->assertFileInterface(
            file: $file,
            filesystemIdentifier: null,
            key: $path,
            fileName: 'foo.txt',
            content: 'test-temporary-file',
            type: 'application/octet-stream',
        );

        $this->assertFileInterface(
            file: $file2,
            filesystemIdentifier: null,
            key: $path,
            fileName: 'foo.txt',
            content: 'test-temporary-file',
            type: 'application/octet-stream',
        );

        $this->assertSame(
            $originalMetadata->get(Constants::FILE_TYPE),
            $entityMetadata->get(Constants::FILE_TYPE),
        );
    }
}
