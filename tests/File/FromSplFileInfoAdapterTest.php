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
use Rekalogika\File\Adapter\FromSplFileInfoAdapter;

class FromSplFileInfoAdapterTest extends TestCase
{
    use FileTestTrait;

    public function testSplFileInfoAdapter(): void
    {
        $path = realpath(__DIR__ . '/../Resources/localFile.txt');
        $this->assertNotFalse($path);
        $splFileInfo = new \SplFileInfo($path);
        $file = FromSplFileInfoAdapter::adapt($splFileInfo);

        $this->assertFileInterface(
            file: $file,
            filesystemIdentifier: null,
            key: $path,
            fileName: 'localFile.txt',
            content: 'test',
            type: 'text/plain',
        );
    }
}
