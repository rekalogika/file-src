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

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\Local\LocalFilesystemAdapter;
use PHPUnit\Framework\TestCase;
use Rekalogika\File\Exception\FilesystemRepository\FilesystemAlreadyExistsException;
use Rekalogika\File\Exception\FilesystemRepository\FilesystemNotFoundException;

class FilesystemRepositoryTest extends TestCase
{
    public function testFilesystemRepository(): void
    {
        $repository = FileFactory::createFilesystemRepository();

        $localFilesystem = $repository->getFilesystem(null);
        $this->assertInstanceOf(FilesystemOperator::class, $localFilesystem);
    }

    public function testAddFilesystem(): void
    {
        $repository = FileFactory::createFilesystemRepository();

        $repository->addFilesystem(
            'test',
            new Filesystem(new LocalFilesystemAdapter('/tmp'))
        );
        $filesystem = $repository->getFilesystem('test');
        $this->assertInstanceOf(FilesystemOperator::class, $filesystem);
    }

    public function testAddMultipleFilesystemError(): void
    {
        $this->expectException(FilesystemAlreadyExistsException::class);
        $repository = FileFactory::createFilesystemRepository();

        $repository->addFilesystem(
            'test',
            new Filesystem(new LocalFilesystemAdapter('/tmp'))
        );

        $repository->addFilesystem(
            'test',
            new Filesystem(new LocalFilesystemAdapter('/tmp'))
        );
    }

    public function testNonExistantFilesystemError(): void
    {
        $this->expectException(FilesystemNotFoundException::class);
        $repository = FileFactory::createFilesystemRepository();
        $repository->getFilesystem('test');
    }
}
