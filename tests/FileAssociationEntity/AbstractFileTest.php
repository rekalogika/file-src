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

namespace Rekalogika\File\Tests\FileAssociationEntity;

use PHPUnit\Framework\TestCase;
use Rekalogika\File\TemporaryFile;
use Rekalogika\File\Tests\File\FileTestTrait;
use Rekalogika\File\Tests\Model\EntityExtendingAbstractFile;

class AbstractFileTest extends TestCase
{
    use FileTestTrait;

    public function testAbstractFile(): void
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

        $entity = new EntityExtendingAbstractFile($file);

        $this->assertFileInterface(
            file: $entity,
            filesystemIdentifier: null,
            key: $path,
            fileName: 'Untitled.txt',
            content: 'test-temporary-file',
            type: 'text/plain',
        );
    }
}
