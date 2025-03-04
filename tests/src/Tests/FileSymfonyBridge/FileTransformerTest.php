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

use PHPUnit\Framework\TestCase;
use Rekalogika\File\Bridge\Symfony\Form\FileTransformer;
use Rekalogika\File\TemporaryFile;
use Symfony\Component\HttpFoundation\File\File;

final class FileTransformerTest extends TestCase
{
    public function testTransform(): void
    {
        $file = TemporaryFile::createFromString('foo');
        $transformer = new FileTransformer();
        $httpFoundationFile = $transformer->transform($file);
        $this->assertSame($file->getKey(), $httpFoundationFile->getPathname());
    }

    public function testReverseTransform(): void
    {
        $httpFoundationFile = new File(__FILE__);
        $transformer = new FileTransformer();
        $file = $transformer->reverseTransform($httpFoundationFile);
        $this->assertSame($file->getKey(), $httpFoundationFile->getPathname());
    }
}
