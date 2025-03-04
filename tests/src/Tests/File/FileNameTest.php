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

namespace Rekalogika\File\Tests\Tests\File;

use PHPUnit\Framework\TestCase;
use Rekalogika\Domain\File\Metadata\Model\FileName;

final class FileNameTest extends TestCase
{
    public function testFileName(): void
    {
        $fileName = new FileName('test.txt');
        $this->assertSame('test.txt', (string) $fileName->getFull());
        $this->assertSame('test', (string) $fileName->getBase());
        $this->assertSame('txt', $fileName->getExtension());
        $this->assertSame('test.txt', (string) $fileName);
        $this->assertTrue($fileName->hasExtension());
    }

    public function testWithoutExtension(): void
    {
        $fileName = new FileName('test');
        $this->assertSame('test', (string) $fileName->getFull());
        $this->assertSame('test', (string) $fileName->getBase());
        $this->assertNull($fileName->getExtension());
        $this->assertSame('test', (string) $fileName);
        $this->assertFalse($fileName->hasExtension());
    }

    public function testWithPath(): void
    {
        $fileName = new FileName('path/to/test.txt');
        $this->assertSame('test.txt', (string) $fileName->getFull());
        $this->assertSame('test', (string) $fileName->getBase());
        $this->assertSame('txt', $fileName->getExtension());
        $this->assertSame('test.txt', (string) $fileName);
        $this->assertTrue($fileName->hasExtension());
    }
}
