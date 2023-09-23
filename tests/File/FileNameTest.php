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
use Rekalogika\File\Name\FileName;

class FileNameTest extends TestCase
{
    public function testFileName(): void
    {
        $fileName = new FileName('test.txt');
        $this->assertSame('test.txt', $fileName->getFull());
        $this->assertSame('test', $fileName->getBase());
        $this->assertSame('txt', $fileName->getExtension());
        $this->assertSame('test.txt', (string) $fileName);
        $this->assertTrue($fileName->hasExtension());
    }

    public function testWithoutExtension(): void
    {
        $fileName = new FileName('test');
        $this->assertSame('test', $fileName->getFull());
        $this->assertSame('test', $fileName->getBase());
        $this->assertNull($fileName->getExtension());
        $this->assertSame('test', (string) $fileName);
        $this->assertFalse($fileName->hasExtension());
    }

    public function testWithPath(): void
    {
        $fileName = new FileName('path/to/test.txt');
        $this->assertSame('test.txt', $fileName->getFull());
        $this->assertSame('test', $fileName->getBase());
        $this->assertSame('txt', $fileName->getExtension());
        $this->assertSame('test.txt', (string) $fileName);
        $this->assertTrue($fileName->hasExtension());
    }
}
