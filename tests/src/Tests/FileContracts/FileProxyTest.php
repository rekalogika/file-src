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

namespace Rekalogika\File\Tests\Tests\FileContracts;

use PHPUnit\Framework\TestCase;
use Rekalogika\Contracts\File\Exception\File\FileNotFoundException;
use Rekalogika\Contracts\File\FileInterface;
use Rekalogika\Contracts\File\FileProxy;
use Rekalogika\File\FilePointer;
use Rekalogika\File\TemporaryFile;
use Rekalogika\File\Tests\Tests\File\FileFactory;
use Rekalogika\File\Tests\Tests\File\FileTestTrait;
use Rekalogika\File\Tests\Tests\Model\EntityWithFileProxyUtilGetterSetter;
use Rekalogika\File\Tests\Tests\Model\EntityWithPlainGetterSetter;

class FileProxyTest extends TestCase
{
    use FileTestTrait;

    public function testProxy(): void
    {
        $fileRepository = FileFactory::createFileRepository();

        $file = TemporaryFile::createFromString('foo', 'test.txt');
        $pointer = $file->getPointer();
        $proxy = new FileProxy($pointer, $fileRepository);

        $this->assertFileInterface(
            file: $proxy,
            filesystemIdentifier: null,
            key: $pointer->getKey(),
            content: 'foo',
            type: 'text/plain',
        );
    }

    public function testProxyToMissingFile(): void
    {
        $fileRepository = FileFactory::createFileRepository();

        $this->expectException(FileNotFoundException::class);
        $file = new FileProxy(new FilePointer('inmemory', 'test'), $fileRepository);
        $file->getContent();
    }

    public function testPlainEntity(): void
    {
        $fileRepository = FileFactory::createFileRepository();
        $missingFile = new FileProxy(new FilePointer('inmemory', 'test'), $fileRepository);

        $entity = new EntityWithPlainGetterSetter($missingFile);

        $this->expectException(FileNotFoundException::class);
        $file = $entity->getFile();
        $this->assertInstanceOf(FileInterface::class, $file);

        $file->getContent();
    }

    public function testEntityWithFileProxyUtil(): void
    {
        $fileRepository = FileFactory::createFileRepository();
        $missingFile = new FileProxy(new FilePointer('inmemory', 'test'), $fileRepository);

        $entity = new EntityWithFileProxyUtilGetterSetter($missingFile);

        $file = $entity->getFile();
        $this->assertNull($file);
    }

    public function testEntitySetter(): void
    {
        $newFile = TemporaryFile::createFromString('foo');

        $entity = new EntityWithFileProxyUtilGetterSetter();
        $entity->setFile($newFile);

        $file = $entity->getFile();
        $this->assertSame($file, $newFile);
    }
}
