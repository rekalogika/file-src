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

namespace Rekalogika\File\Tests\FileImage;

use PHPUnit\Framework\TestCase;
use Rekalogika\Contracts\File\FileRepositoryInterface;
use Rekalogika\Contracts\File\Metadata\ImageMetadataInterface;
use Rekalogika\File\FilePointer;
use Rekalogika\File\Image\ImageResizer;
use Rekalogika\File\Tests\File\FileFactory;

class ImageResizerTest extends TestCase
{
    private FileRepositoryInterface $fileRepository;

    public function setUp(): void
    {
        $this->fileRepository = FileFactory::createFileRepository();
    }

    public function testImageResizer(): void
    {
        $file = $this->fileRepository->createFromLocalFile(
            new FilePointer('inmemory', 'smiley'),
            __DIR__ . '/../Resources/smiley.png'
        );

        $imageResizer = new ImageResizer();
        $imageResizer->setFileRepository($this->fileRepository);

        $resizedFile = $imageResizer
            ->take($file)
            ->resize(120, ImageResizer::ASPECTRATIO_SQUARE)
            ->getResult();

        $this->assertEquals(120, $resizedFile->get(ImageMetadataInterface::class)?->getWidth());
        $this->assertEquals(120, $resizedFile->get(ImageMetadataInterface::class)?->getHeight());
        $this->assertEquals('smiley.d/square-120', $resizedFile->getKey());
    }
}
