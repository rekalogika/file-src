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
use Rekalogika\File\TemporaryFile;
use Rekalogika\File\Zip\FileZip;

class ZipTest extends TestCase
{
    public function testZip(): void
    {
        $temporaryFile = tempnam(sys_get_temp_dir(), 'zip');
        $this->assertNotFalse($temporaryFile);
        $output = fopen($temporaryFile, 'wb');
        $this->assertNotFalse($output);

        $file1 = TemporaryFile::createFromString('file1');
        $file2 = TemporaryFile::createFromString('file2');
        $file3 = TemporaryFile::createFromString('file3');
        $file3a = TemporaryFile::createFromString('file3a');
        $fileInSubDir1 = TemporaryFile::createFromString('fileInSubDir1');
        $fileInSubDir2 = TemporaryFile::createFromString('fileInSubDir2');
        $fileInSubDir2a = TemporaryFile::createFromString('fileInSubDir2a');

        $file1->setName('file1.txt');
        $file2->setName('file2.txt');
        $file3->setName('file3.txt');
        $file3a->setName('file3.txt');
        $fileInSubDir1->setName('fileInSubDir1.txt');
        $fileInSubDir2->setName('fileInSubDir2.txt');
        $fileInSubDir2a->setName('fileInSubDir2.txt');

        $zip = new FileZip();
        $zip->zipFiles(
            fileName: 'test.zip',
            files: [
                $file1,
                $file2,
                $file3,
                $file3a,
                'subdir' => [
                    $fileInSubDir1,
                    $fileInSubDir2,
                    $fileInSubDir2a,
                ]
            ],
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
