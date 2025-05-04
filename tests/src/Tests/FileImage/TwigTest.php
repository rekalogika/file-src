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

use Rekalogika\Contracts\File\FileRepositoryInterface;
use Rekalogika\File\FilePointer;
use Rekalogika\File\Image\ImageResizer;
use Rekalogika\File\Image\ImageTwigExtension;
use Rekalogika\File\Image\ImageTwigRuntime;
use Rekalogika\File\Tests\Tests\File\FileFactory;
use Twig\RuntimeLoader\FactoryRuntimeLoader;
use Twig\Test\IntegrationTestCase;

final class TwigTest extends IntegrationTestCase
{
    private FileRepositoryInterface $fileRepository;
    private ImageResizer $imageResizer;


    #[\Override]
    protected function setUp(): void
    {
        $this->fileRepository = FileFactory::createFileRepository();
        $this->imageResizer = new ImageResizer();
        $this->imageResizer->setFileRepository($this->fileRepository);
        parent::setUp();
    }

    #[\Override]
    protected function getExtensions(): array
    {
        $image = $this->fileRepository->createFromLocalFile(
            new FilePointer('inmemory', 'smiley'),
            __DIR__ . '/../Resources/smiley.png',
        );

        return [
            new ImageTwigExtension(),
            new TwigTestExtension([
                'image' => $image,
            ]),
        ];
    }

    #[\Override]
    protected function getRuntimeLoaders()
    {
        return [
            new FactoryRuntimeLoader([
                ImageTwigRuntime::class => function () {
                    return new ImageTwigRuntime($this->imageResizer);
                },
            ]),
        ];
    }

    #[\Override]
    protected static function getFixturesDirectory(): string
    {
        return __DIR__ . '/Fixtures/';
    }

    protected function getFixturesDir(): string
    {
        return __DIR__ . '/Fixtures/';
    }
}
