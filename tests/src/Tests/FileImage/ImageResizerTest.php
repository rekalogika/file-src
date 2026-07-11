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

namespace Rekalogika\File\Tests\Tests\FileImage;

use PHPUnit\Framework\TestCase;
use Rekalogika\Contracts\File\FileRepositoryInterface;
use Rekalogika\Contracts\File\Metadata\ImageMetadataInterface;
use Rekalogika\File\FilePointer;
use Rekalogika\File\Image\ImageResizer;
use Rekalogika\File\Image\ImageTwigRuntime;
use Rekalogika\File\TemporaryFile;
use Rekalogika\File\Tests\Tests\File\FileFactory;

final class ImageResizerTest extends TestCase
{
    private FileRepositoryInterface $fileRepository;

    #[\Override]
    protected function setUp(): void
    {
        $this->fileRepository = FileFactory::createFileRepository();
    }

    public function testImageResizer(): void
    {
        $file = $this->fileRepository->createFromLocalFile(
            new FilePointer('inmemory', 'smiley'),
            __DIR__ . '/../Resources/smiley.png',
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

    /**
     * A file that has been submitted through a form, but is not yet persisted,
     * lives in an unbounded local filesystem, and therefore cannot host a
     * derivation. Resizing it must still work: the result is simply not cached.
     * This is what happens when a form containing a FilePondType is redisplayed
     * after a failed validation.
     */
    public function testResizeFileThatCannotHostADerivation(): void
    {
        $file = TemporaryFile::createFromString(
            (string) file_get_contents(__DIR__ . '/../Resources/smiley.png'),
            'filepond-',
        );

        $imageResizer = new ImageResizer();
        $imageResizer->setFileRepository($this->fileRepository);

        $resizedFile = $imageResizer
            ->take($file)
            ->resize(120, ImageResizer::ASPECTRATIO_SQUARE)
            ->getResult();

        $this->assertEquals(120, $resizedFile->get(ImageMetadataInterface::class)?->getWidth());
        $this->assertEquals(120, $resizedFile->get(ImageMetadataInterface::class)?->getHeight());
        $this->assertNull($resizedFile->getFilesystemIdentifier());
    }

    /**
     * Such a result is gone by the next request, so it cannot be served through
     * a temporary URL. It has to be rendered inline instead.
     */
    public function testDataUriOfFileThatCannotHostADerivation(): void
    {
        $file = TemporaryFile::createFromString(
            (string) file_get_contents(__DIR__ . '/../Resources/smiley.png'),
            'filepond-',
        );

        $imageResizer = new ImageResizer();
        $imageResizer->setFileRepository($this->fileRepository);

        $runtime = new ImageTwigRuntime($imageResizer);
        $dataUri = (string) $runtime->fileDataUri($runtime->fileImageResize($file, 120));

        $this->assertStringStartsWith('data:image/png;base64,', $dataUri);

        $decoded = base64_decode(substr($dataUri, \strlen('data:image/png;base64,')), true);
        $this->assertIsString($decoded);

        $size = getimagesizefromstring($decoded);
        $this->assertIsArray($size);
        $this->assertEquals(120, $size[0]);
    }

    public function testCorruptImage(): void
    {
        $file = $this->fileRepository->createFromLocalFile(
            new FilePointer('inmemory', 'corrupt'),
            __DIR__ . '/../Resources/corrupt.jpg',
        );

        $imageResizer = new ImageResizer();
        $imageResizer->setFileRepository($this->fileRepository);

        $resizedFile = $imageResizer
            ->take($file)
            ->resize(120, ImageResizer::ASPECTRATIO_SQUARE)
            ->getResult();

        $this->assertEquals(120, $resizedFile->get(ImageMetadataInterface::class)?->getWidth());
        $this->assertEquals(120, $resizedFile->get(ImageMetadataInterface::class)?->getHeight());
        $this->assertEquals('corrupt.d/square-120', $resizedFile->getKey());
    }
}
