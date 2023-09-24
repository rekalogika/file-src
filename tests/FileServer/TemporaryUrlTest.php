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

namespace Rekalogika\File\Tests\FileBundle;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Rekalogika\File\File;
use Rekalogika\File\FilePointer;
use Rekalogika\File\TemporaryFile;
use Rekalogika\File\Tests\TestKernel;
use Rekalogika\TemporaryUrl\TemporaryUrlGeneratorInterface;

class TemporaryUrlTest extends TestCase
{
    private ?ContainerInterface $container = null;

    public function setUp(): void
    {
        $kernel = new TestKernel();
        $kernel->boot();
        $this->container = $kernel->getContainer();
    }

    public function testTemporaryUrlWithFilePointer(): void
    {
        $temporaryUrlGenerator = $this->container
            ?->get('test.' . TemporaryUrlGeneratorInterface::class);

        $this->assertInstanceOf(
            TemporaryUrlGeneratorInterface::class,
            $temporaryUrlGenerator
        );

        $filePointer = new FilePointer('default', 'test.txt');
        $url = $temporaryUrlGenerator->generateUrl($filePointer);

        $this->assertStringContainsString('__route__', $url);
    }

    public function testTemporaryUrlWithFile(): void
    {
        $temporaryUrlGenerator = $this->container
            ?->get('test.' . TemporaryUrlGeneratorInterface::class);

        $this->assertInstanceOf(
            TemporaryUrlGeneratorInterface::class,
            $temporaryUrlGenerator
        );

        $file = new File(__DIR__ . '/../Resources/localFile.txt');

        $url = $temporaryUrlGenerator->generateUrl($file);

        $this->assertStringContainsString('__route__', $url);
    }
}
