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

use PHPUnit\Framework\TestCase;
use Rekalogika\Contracts\File\Exception\File\FileNotFoundException;
use Rekalogika\Contracts\File\FileRepositoryInterface;
use Rekalogika\Domain\File\Metadata\Constants;
use Rekalogika\File\FilePointer;

class FileRepositoryTest extends TestCase
{
    use FileTestTrait;

    private FileRepositoryInterface $fileRepository;

    #[\Override]
    protected function setUp(): void
    {
        $this->fileRepository = FileFactory::createFileRepository();
    }

    public function testCreateAndRead(): void
    {
        $pointer = new FilePointer('inmemory', 'writetest');

        // write
        $file = $this->fileRepository->createFromString($pointer, 'content', [
            Constants::FILE_NAME => 'test.txt',
        ]);

        $this->assertFileInterface(
            file: $file,
            filesystemIdentifier: 'inmemory',
            key: 'writetest',
            fileName: 'test.txt',
            content: 'content',
            type: 'text/plain',
        );

        // clear
        $this->fileRepository->clear();

        // getFile
        $file = $this->fileRepository->get($pointer);

        $this->assertFileInterface(
            file: $file,
            filesystemIdentifier: 'inmemory',
            key: 'writetest',
            fileName: 'test.txt',
            content: 'content',
            type: 'text/plain',
        );
    }

    public function testCreateFromStream(): void
    {
        // write from stream
        $pointer = new FilePointer('inmemory', 'writestreamtest');
        $stream = fopen(__DIR__ . '/../Resources/localFile.txt', 'rb');
        $this->assertNotFalse($stream);

        $fileFromStream = $this->fileRepository->createFromStream($pointer, $stream, [
            Constants::FILE_NAME => 'test.txt',
        ]);

        $this->assertFileInterface(
            file: $fileFromStream,
            filesystemIdentifier: 'inmemory',
            key: 'writestreamtest',
            fileName: 'test.txt',
            content: 'test',
            type: 'text/plain',
        );
    }

    public function testCreateFromLocalFile(): void
    {
        // write from localfile
        $pointer = new FilePointer('inmemory', 'writelocalfiletest');
        $localFile = __DIR__ . '/../Resources/localFile.txt';

        $fileFromLocalFile = $this->fileRepository->createFromLocalFile($pointer, $localFile, [
            Constants::FILE_NAME => 'test.txt',
        ]);

        $this->assertFileInterface(
            file: $fileFromLocalFile,
            filesystemIdentifier: 'inmemory',
            key: 'writelocalfiletest',
            fileName: 'test.txt',
            content: 'test',
            type: 'text/plain',
        );
    }

    public function testDelete(): void
    {
        $pointer = new FilePointer('inmemory', 'deletetest');

        // write
        $file = $this->fileRepository->createFromString($pointer, 'content', [
            Constants::FILE_NAME => 'test.txt',
        ]);

        $this->assertFileInterface(
            file: $file,
            filesystemIdentifier: 'inmemory',
            key: 'deletetest',
            fileName: 'test.txt',
            content: 'content',
            type: 'text/plain',
        );

        // delete
        $this->fileRepository->delete($pointer);

        // getFile
        $this->expectException(FileNotFoundException::class);
        $this->fileRepository->get($pointer);
    }

    public function testCopy(): void
    {
        $pointer = new FilePointer('inmemory', 'copytest');
        $pointer2 = new FilePointer('inmemory', 'copytest2');

        // write
        $file = $this->fileRepository->createFromString($pointer, 'content', [
            Constants::FILE_NAME => 'test.txt',
        ]);

        $this->assertFileInterface(
            file: $file,
            filesystemIdentifier: 'inmemory',
            key: 'copytest',
            fileName: 'test.txt',
            content: 'content',
            type: 'text/plain',
        );

        // copy
        $this->fileRepository->copy($pointer, $pointer2);
        $file2 = $this->fileRepository->get($pointer2);

        $this->assertFileInterface(
            file: $file2,
            filesystemIdentifier: 'inmemory',
            key: 'copytest2',
            fileName: 'test.txt',
            content: 'content',
            type: 'text/plain',
        );
    }

    public function testCopyCrossingFilesystem(): void
    {
        $pointer = new FilePointer('inmemory', 'copytest');
        $pointer2 = new FilePointer('inmemory2', 'copytest2');

        // write
        $file = $this->fileRepository->createFromString($pointer, 'content', [
            Constants::FILE_NAME => 'test.txt',
        ]);

        $this->assertFileInterface(
            file: $file,
            filesystemIdentifier: 'inmemory',
            key: 'copytest',
            fileName: 'test.txt',
            content: 'content',
            type: 'text/plain',
        );

        // copy
        $this->fileRepository->copy($pointer, $pointer2);
        $file2 = $this->fileRepository->get($pointer2);

        $this->assertFileInterface(
            file: $file2,
            filesystemIdentifier: 'inmemory2',
            key: 'copytest2',
            fileName: 'test.txt',
            content: 'content',
            type: 'text/plain',
        );
    }

    public function testMove(): void
    {
        $pointer = new FilePointer('inmemory', 'movetest');
        $pointer2 = new FilePointer('inmemory', 'movetest2');

        // write
        $file = $this->fileRepository->createFromString($pointer, 'content', [
            Constants::FILE_NAME => 'test.txt',
        ]);

        $this->assertFileInterface(
            file: $file,
            filesystemIdentifier: 'inmemory',
            key: 'movetest',
            fileName: 'test.txt',
            content: 'content',
            type: 'text/plain',
        );

        // move
        $this->fileRepository->move($pointer, $pointer2);

        // getFile
        $this->expectException(FileNotFoundException::class);
        $this->fileRepository->get($pointer);

        // getFile
        $file2 = $this->fileRepository->get($pointer2);

        $this->assertFileInterface(
            file: $file2,
            filesystemIdentifier: 'inmemory',
            key: 'movetest2',
            fileName: 'test.txt',
            content: 'content',
            type: 'text/plain',
        );
    }

    public function testMoveCrossingFilesystem(): void
    {
        $pointer = new FilePointer('inmemory', 'movetest');
        $pointer2 = new FilePointer('inmemory2', 'movetest2');

        // write
        $file = $this->fileRepository->createFromString($pointer, 'content', [
            Constants::FILE_NAME => 'test.txt',
        ]);

        $this->assertFileInterface(
            file: $file,
            filesystemIdentifier: 'inmemory',
            key: 'movetest',
            fileName: 'test.txt',
            content: 'content',
            type: 'text/plain',
        );

        // move
        $this->fileRepository->move($pointer, $pointer2);

        // getFile
        $this->expectException(FileNotFoundException::class);
        $this->fileRepository->get($pointer);

        // getFile
        $file2 = $this->fileRepository->get($pointer2);

        $this->assertFileInterface(
            file: $file2,
            filesystemIdentifier: 'inmemory2',
            key: 'movetest2',
            fileName: 'test.txt',
            content: 'content',
            type: 'text/plain',
        );
    }

    public function testTemporaryFileAndWrite(): void
    {
        $temporaryFile = $this->fileRepository
            ->createTemporaryFile('tmp/prefix', 'inmemory');

        $this->assertFileInterface(
            file: $temporaryFile,
            filesystemIdentifier: 'inmemory',
            key: $temporaryFile->getKey(),
            fileName: 'Untitled',
            content: '',
            type: 'application/x-empty',
        );

        $temporaryFile->setContent('content');

        $this->assertFileInterface(
            file: $temporaryFile,
            filesystemIdentifier: 'inmemory',
            key: $temporaryFile->getKey(),
            fileName: 'Untitled.txt',
            content: 'content',
            type: 'text/plain',
        );

        $image = fopen(__DIR__ . '/../Resources/smiley.png', 'rb');
        $this->assertNotFalse($image);
        $imageContent = \file_get_contents(__DIR__ . '/../Resources/smiley.png');
        $this->assertNotFalse($imageContent);

        $temporaryFile->setContentFromStream($image);

        $this->assertFileInterface(
            file: $temporaryFile,
            filesystemIdentifier: 'inmemory',
            key: $temporaryFile->getKey(),
            fileName: 'Untitled.png',
            content: $imageContent,
            type: 'image/png',
            width: 240,
            height: 240,
        );

        $temporaryFile->setName('smiley.png');

        $this->assertFileInterface(
            file: $temporaryFile,
            filesystemIdentifier: 'inmemory',
            key: $temporaryFile->getKey(),
            fileName: 'smiley.png',
            content: $imageContent,
            type: 'image/png',
            width: 240,
            height: 240,
        );
    }
}
