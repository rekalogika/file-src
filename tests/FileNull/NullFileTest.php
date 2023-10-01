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

namespace Rekalogika\File\Tests\FileNull;

use PHPUnit\Framework\TestCase;
use Rekalogika\Contracts\File\FileInterface;
use Rekalogika\Contracts\File\FilePointerInterface;
use Rekalogika\Domain\File\Null\NullFile;
use Rekalogika\Domain\File\Null\NullName;
use Rekalogika\Domain\File\Null\NullPointer;
use Rekalogika\Domain\File\Null\NullType;
use Rekalogika\File\File;

class NullFileTest extends TestCase
{
    public function testNullFile(): void
    {
        $nullFile = new NullFile();
        $nullType = $nullFile->getType();
        $nullName = $nullFile->getName();
        $nullPointer = $nullFile->getPointer();

        $this->assertInstanceOf(NullFile::class, $nullFile);
        $this->assertInstanceOf(NullType::class, $nullType);
        $this->assertInstanceOf(NullName::class, $nullName);
        $this->assertInstanceOf(NullPointer::class, $nullPointer);
    }

    /**
     * @dataProvider equalityProvider
     */
    public function testEquality(
        bool $expected,
        FilePointerInterface|FileInterface $file1,
        FilePointerInterface|FileInterface $file2,
    ): void {
        $this->assertSame($expected, $file1->isEqualTo($file2));
        $this->assertSame($expected, $file2->isEqualTo($file1));
        $this->assertSame($expected, $file1->isSameFilesystem($file2));
        $this->assertSame($expected, $file2->isSameFilesystem($file1));
    }

    /**
     * @return iterable<int,array{0:bool,1:FilePointerInterface|FileInterface,2:FilePointerInterface|FileInterface}>
     */
    public function equalityProvider(): iterable
    {
        $nullFile = new NullFile();
        $nullPointer = $nullFile->getPointer();
        $realFile = new File(__FILE__);
        $realPointer = $realFile->getPointer();

        $all = [$nullFile, $nullPointer, $realFile, $realPointer];

        foreach ($all as $file1) {
            foreach ($all as $file2) {
                yield [
                    !$file1 instanceof NullFile
                        && !$file2 instanceof NullFile
                        && !$file1 instanceof NullPointer
                        && !$file2 instanceof NullPointer,
                    $file1,
                    $file2,
                ];
            }
        }
    }
}
