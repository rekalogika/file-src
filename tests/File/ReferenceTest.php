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
use Rekalogika\Contracts\File\FileInterface;
use Rekalogika\Contracts\File\FileProxy;
use Rekalogika\Contracts\File\FileRepositoryInterface;
use Rekalogika\DirectPropertyAccess\DirectPropertyAccessor;
use Rekalogika\File\FilePointer;
use Rekalogika\File\Metadata\Metadata;

class ReferenceTest extends TestCase
{
    use FileTestTrait;

    private FileRepositoryInterface $fileRepository;

    public function setUp(): void
    {
        $this->fileRepository = FileFactory::createFileRepository();
    }

    public function testValidReference(): void
    {
        $pointer = new FilePointer('inmemory', 'writetest');

        $file = $this->fileRepository->createFromString($pointer, 'content', [
            Metadata::FILE_NAME => 'test.txt',
        ]);

        $this->assertFileInterface(
            file: $file,
            filesystemIdentifier: 'inmemory',
            key: 'writetest',
            fileName: 'test.txt',
            content: 'content',
            type: 'text/plain',
        );

        $this->fileRepository->clear();

        $propertyAccess = new DirectPropertyAccessor();

        $proxy = $this->fileRepository->getReference($pointer);
        $wrapped = $propertyAccess->getValue($proxy, 'wrapped');
        $this->assertNull($wrapped);

        $this->assertFileInterface(
            file: $proxy,
            filesystemIdentifier: 'inmemory',
            key: 'writetest',
            fileName: 'test.txt',
            content: 'content',
            type: 'text/plain',
        );

        /** @var ?FileInterface */
        $wrapped = $propertyAccess->getValue($proxy, 'wrapped');
        $this->assertInstanceOf(FileInterface::class, $wrapped);
    }

    public function testMissingReference(): void
    {
        $pointer = new FilePointer('inmemory', 'foo');

        $propertyAccess = new DirectPropertyAccessor();

        $proxy = $this->fileRepository->getReference($pointer);
        $wrapped = $propertyAccess->getValue($proxy, 'wrapped');
        $this->assertNull($wrapped);

        $this->expectException(FileNotFoundException::class);

        $proxy->getContent();
    }

    public function testProxyGetter(): void
    {
        $pointer = new FilePointer('inmemory', 'writetest');

        $file = $this->fileRepository->createFromString($pointer, 'content', [
            Metadata::FILE_NAME => 'test.txt',
        ]);

        $this->assertFileInterface(
            file: $file,
            filesystemIdentifier: 'inmemory',
            key: 'writetest',
            fileName: 'test.txt',
            content: 'content',
            type: 'text/plain',
        );

        $this->fileRepository->clear();

        $proxy = $this->fileRepository->getReference($pointer);

        $realFile = FileProxy::getFile($proxy);
        $this->assertNotNull($realFile);

        $this->assertFileInterface(
            file: $realFile,
            filesystemIdentifier: 'inmemory',
            key: 'writetest',
            fileName: 'test.txt',
            content: 'content',
            type: 'text/plain',
        );
    }

    public function testProxyGetterWithMissingReference(): void
    {
        $pointer = new FilePointer('inmemory', 'foo');

        $propertyAccess = new DirectPropertyAccessor();

        $proxy = $this->fileRepository->getReference($pointer);
        $wrapped = $propertyAccess->getValue($proxy, 'wrapped');
        $this->assertNull($wrapped);

        $realFile = FileProxy::getFile($proxy);
        $this->assertNull($realFile);
    }
}
