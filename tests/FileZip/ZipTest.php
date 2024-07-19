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

namespace Rekalogika\File\Tests\FileZip;

use PHPUnit\Framework\TestCase;
use Rekalogika\Contracts\File\FileInterface;
use Rekalogika\Contracts\File\FileRepositoryInterface;
use Rekalogika\File\FilePointer;
use Rekalogika\File\Tests\TestKernel;
use Rekalogika\File\Zip\FileZip;
use Rekalogika\File\Zip\Model\Directory;

class ZipTest extends TestCase
{
    private ?FileRepositoryInterface $fileRepository = null;

    private ?FileZip $fileZip = null;

    public function setUp(): void
    {
        $kernel = new TestKernel();
        $kernel->boot();
        $container = $kernel->getContainer();

        $fileRepository = $container
            ->get('test.' . FileRepositoryInterface::class);

        $this->assertInstanceOf(
            FileRepositoryInterface::class,
            $fileRepository
        );

        $this->fileRepository = $fileRepository;

        $fileZip = $container
            ->get('test.' . FileZip::class);

        $this->assertInstanceOf(
            FileZip::class,
            $fileZip
        );

        $this->fileZip = $fileZip;
    }

    private function createFile(string $key, string $content): FileInterface
    {
        $pointer = new FilePointer('default', $key);
        $file = $this->fileRepository?->createFromString($pointer, $content);
        $this->assertInstanceOf(FileInterface::class, $file);

        return $file;
    }

    public function testZip(): void
    {
        $temporaryFile = tempnam(sys_get_temp_dir(), 'zip');
        $this->assertNotFalse($temporaryFile);
        $output = fopen($temporaryFile, 'wb');
        $this->assertNotFalse($output);

        $file1 = $this->createFile('file1', 'file1');
        $file2 = $this->createFile('file2', 'file2');
        $file3 = $this->createFile('file3', 'file3');
        $file3a = $this->createFile('file3a', 'file3a');
        $fileInSubDir1 = $this->createFile('fileInSubDir1', 'fileInSubDir1');
        $fileInSubDir2 = $this->createFile('fileInSubDir2', 'fileInSubDir2');
        $fileInSubDir2a = $this->createFile('fileInSubDir2a', 'fileInSubDir2a');

        $file1->setName('file1.txt');
        $file2->setName('file2.txt');
        $file3->setName('file3.txt');
        $file3a->setName('file3.txt');
        $fileInSubDir1->setName('fileInSubDir1.txt');
        $fileInSubDir2->setName('fileInSubDir2.txt');
        $fileInSubDir2a->setName('fileInSubDir2.txt');

        $rootDirectory = new Directory('test.zip');
        $rootDirectory->addPointer($file1->getPointer());
        $rootDirectory->addPointer($file2->getPointer());
        $rootDirectory->addPointer($file3->getPointer());
        $rootDirectory->addPointer($file3a->getPointer());
        $subdir = $rootDirectory->createDirectory('subdir');
        $subdir->addPointer($fileInSubDir1->getPointer());
        $subdir->addPointer($fileInSubDir2->getPointer());
        $subdir->addPointer($fileInSubDir2a->getPointer());

        $this->fileZip?->streamZip(
            directory: $rootDirectory,
            outputStream: $output,
            sendHttpHeaders: false,
        );

        $this->assertFileExists($temporaryFile);
        $this->assertFileIsReadable($temporaryFile);
        $this->assertFileIsWritable($temporaryFile);

        $this->assertEquals(
            'file1',
            file_get_contents('zip://' . $temporaryFile . '#file1.txt')
        );

        $this->assertEquals(
            'file2',
            file_get_contents('zip://' . $temporaryFile . '#file2.txt')
        );

        $this->assertEquals(
            'file3',
            file_get_contents('zip://' . $temporaryFile . '#file3.txt')
        );

        $this->assertEquals(
            'file3a',
            file_get_contents('zip://' . $temporaryFile . '#file3 (1).txt')
        );

        $this->assertEquals(
            'fileInSubDir1',
            file_get_contents('zip://' . $temporaryFile . '#subdir/fileInSubDir1.txt')
        );

        $this->assertEquals(
            'fileInSubDir2',
            file_get_contents('zip://' . $temporaryFile . '#subdir/fileInSubDir2.txt')
        );

        $this->assertEquals(
            'fileInSubDir2a',
            file_get_contents('zip://' . $temporaryFile . '#subdir/fileInSubDir2 (1).txt')
        );

        fclose($output);
        unlink($temporaryFile);
    }
}
