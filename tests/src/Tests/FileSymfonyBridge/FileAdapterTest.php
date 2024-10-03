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

namespace Rekalogika\File\Tests\Tests\FileSymfonyBridge;

use Rekalogika\Contracts\File\FileInterface;
use Rekalogika\Contracts\File\FileRepositoryInterface;
use Rekalogika\File\Bridge\Symfony\HttpFoundation\FromHttpFoundationFileAdapter;
use Rekalogika\File\Bridge\Symfony\HttpFoundation\ToHttpFoundationFileAdapter;
use Rekalogika\File\FilePointer;
use Rekalogika\File\LocalTemporaryFile;
use Rekalogika\File\TemporaryFile;
use Rekalogika\File\Tests\Tests\File\FileTestTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\File\File;

class FileAdapterTest extends KernelTestCase
{
    use FileTestTrait;

    private function createRemoteFile(): FileInterface
    {
        $fileRepository = static::getContainer()
            ->get(FileRepositoryInterface::class);

        $this->assertInstanceOf(FileRepositoryInterface::class, $fileRepository);

        return $fileRepository->createFromString(new FilePointer('default', 'key'), 'asdf');
    }

    public function testAdaptFromHttpFoundationFileToFileInterface(): void
    {
        $localTemporaryFile = LocalTemporaryFile::createFromString('foo');
        $httpFoundationFile = new File($localTemporaryFile->getPathname());

        $file = FromHttpFoundationFileAdapter::adapt($httpFoundationFile);

        $this->assertFileInterface(
            file: $file,
            filesystemIdentifier: null,
            key: $localTemporaryFile->getPathname(),
            content: 'foo',
            type: 'text/plain',
        );
    }

    public function testAdaptFromFileInterfaceToHttpFoundationFile(): void
    {
        $file = TemporaryFile::createFromString('foo');
        $httpFoundationFile = ToHttpFoundationFileAdapter::adapt($file);

        $this->assertSame('foo', $httpFoundationFile->getContent());
        $this->assertSame('text/plain', $httpFoundationFile->getMimeType());
        $this->assertSame($file->getKey(), $httpFoundationFile->getPathname());
    }

    public function testRemoteAdaptFromFileInterfaceToHttpFoundationFile(): void
    {
        $file = $this->createRemoteFile();

        $httpFoundationFile = ToHttpFoundationFileAdapter::adapt($file);

        $this->assertSame('asdf', $httpFoundationFile->getContent());
        $this->assertSame('text/plain', $httpFoundationFile->getMimeType());
    }

    public function testLocalFileBypass(): void
    {
        $file = TemporaryFile::createFromString('foo');
        $httpFoundationFile = ToHttpFoundationFileAdapter::adapt($file);
        $this->assertNotInstanceOf(ToHttpFoundationFileAdapter::class, $httpFoundationFile);
    }

    public function testRemoteFileNonBypass(): void
    {
        $file = $this->createRemoteFile();

        $httpFoundationFile = ToHttpFoundationFileAdapter::adapt($file);
        $this->assertInstanceOf(ToHttpFoundationFileAdapter::class, $httpFoundationFile);
    }

    public function testAdaptception(): void
    {
        $file = $this->createRemoteFile();
        $httpFoundationFile = ToHttpFoundationFileAdapter::adapt($file);
        $this->assertInstanceOf(ToHttpFoundationFileAdapter::class, $httpFoundationFile);

        $file2 = FromHttpFoundationFileAdapter::adapt($httpFoundationFile);
        $this->assertSame($file, $file2);
    }
}
