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
use Rekalogika\Contracts\File\RawMetadataInterface;
use Rekalogika\Domain\File\Metadata\Constants;
use Rekalogika\File\File;
use Rekalogika\File\TemporaryFile;

class FileTest extends TestCase
{
    use FileTestTrait;

    public function testLocalFile(): void
    {
        $dir = __DIR__ . '/../../var/test';

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $path = $dir . '/test.txt';

        \file_put_contents($path, 'test');

        $file = new File($path);
        $path = \realpath($path);
        $this->assertNotFalse($path);

        $this->assertFileInterface(
            file: $file,
            filesystemIdentifier: null,
            key: $path,
            fileName: 'test.txt',
            content: 'test',
            type: 'text/plain',
        );

        @unlink($path);
    }

    public function testTemporaryFileCreate(): void
    {
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

        unset($file);
        $this->assertFileDoesNotExist($path);
    }

    public function testTemporaryFileCreateFromString(): void
    {
        $file = TemporaryFile::createFromString('test-temporary-file-from-string', 'test');
        $path = $file->getKey();

        $this->assertFileInterface(
            file: $file,
            filesystemIdentifier: null,
            key: $path,
            content: 'test-temporary-file-from-string',
            type: 'text/plain',
        );

        unset($file);
        $this->assertFileDoesNotExist($path);
    }

    public function testNaming(): void
    {
        $png = file_get_contents(__DIR__ . '/../Resources/smiley.png');
        $this->assertNotFalse($png);

        $file = TemporaryFile::createFromString($png);
        $path = $file->getKey();

        $this->assertFileInterface(
            file: $file,
            filesystemIdentifier: null,
            key: $path,
            fileName: 'Untitled.png',
            content: $png,
            type: 'image/png',
        );

        $file->setName('foo.png');

        $this->assertFileInterface(
            file: $file,
            filesystemIdentifier: null,
            key: $path,
            fileName: 'foo.png',
            content: $png,
            type: 'image/png',
        );

        $file->setName('bar');

        $this->assertFileInterface(
            file: $file,
            filesystemIdentifier: null,
            key: $path,
            fileName: 'bar.png',
            content: $png,
            type: 'image/png',
        );

        $file->get(RawMetadataInterface::class)?->set(Constants::FILE_NAME, 'foo');

        $this->assertFileInterface(
            file: $file,
            filesystemIdentifier: null,
            key: $path,
            fileName: 'foo',
            content: $png,
            type: 'image/png',
        );
    }

    public function testOpenBasedir(): void
    {
        $dir = realpath(__DIR__ . '/../../');
        ini_set('open_basedir', $dir . ":" . '/tmp');
        $path = $dir . '/var/test.txt';
        \file_put_contents($path, 'foo');

        $file = new File($path);
        $content = $file->getContent();
        $file->setContent('bar');
        $newContent = $file->getContent();

        $this->assertSame('foo', $content);
        $this->assertSame('bar', $newContent);
    }
}
