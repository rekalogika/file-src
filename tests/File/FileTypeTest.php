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
use Rekalogika\File\Type\MimeMapFileTypeAdapter;

class FileTypeTest extends TestCase
{
    public function testFileType(): void
    {
        $type = new MimeMapFileTypeAdapter('image/jpeg');
        $this->assertSame('image/jpeg', $type->getName());
        $this->assertSame('image', $type->getType());
        $this->assertSame('jpeg', $type->getSubType());
        $this->assertSame(['jpeg', 'jpg', 'jpe'], $type->getCommonExtensions());
        $this->assertSame('jpeg', $type->getExtension());
        $this->assertSame('JPEG image', (string) $type->getDescription());
        $this->assertSame('image/jpeg', (string) $type);
    }
}
