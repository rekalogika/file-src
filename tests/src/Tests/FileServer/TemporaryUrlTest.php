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

namespace Rekalogika\File\Tests\Tests\FileServer;

use Rekalogika\File\File;
use Rekalogika\File\FilePointer;
use Rekalogika\TemporaryUrl\TemporaryUrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TemporaryUrlTest extends KernelTestCase
{
    public function testTemporaryUrlWithFilePointer(): void
    {
        $this->markTestSkipped();

        // $temporaryUrlGenerator = static::getContainer()
        //     ->get(TemporaryUrlGeneratorInterface::class);

        // $this->assertInstanceOf(
        //     TemporaryUrlGeneratorInterface::class,
        //     $temporaryUrlGenerator,
        // );

        // $filePointer = new FilePointer('default', 'test.txt');
        // $url = $temporaryUrlGenerator->generateUrl($filePointer);

        // $this->assertStringContainsString('__route__', $url);
    }

    public function testTemporaryUrlWithFile(): void
    {
        $this->markTestSkipped();

        // $temporaryUrlGenerator = static::getContainer()
        //     ->get(TemporaryUrlGeneratorInterface::class);

        // $this->assertInstanceOf(
        //     TemporaryUrlGeneratorInterface::class,
        //     $temporaryUrlGenerator,
        // );

        // $file = new File(__DIR__ . '/../Resources/localFile.txt');

        // $url = $temporaryUrlGenerator->generateUrl($file);

        // $this->assertStringContainsString('__route__', $url);
    }
}
