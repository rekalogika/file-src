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

namespace Rekalogika\File\Tests;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\InMemory\InMemoryFilesystemAdapter;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Factory
{
    public static function createUrlGenerator(): UrlGeneratorInterface
    {
        $urlGenerator = \Mockery::mock(UrlGeneratorInterface::class);
        $urlGenerator->shouldReceive('generate')
            ->andReturn('__route__');

        return $urlGenerator;
    }

    public static function createTestFilesystem(): FilesystemOperator
    {
        $filesystem = new Filesystem(new InMemoryFilesystemAdapter());

        return $filesystem;
    }
}
