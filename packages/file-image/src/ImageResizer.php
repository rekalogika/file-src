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

namespace Rekalogika\File\Image;

use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Encoders\AutoEncoder;
use Intervention\Image\Encoders\MediaTypeEncoder;
use Intervention\Image\Exceptions\EncoderException;
use Intervention\Image\Exceptions\NotSupportedException;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\ImageInterface;
use Psr\Log\LoggerInterface;
use Rekalogika\Contracts\File\FileInterface;
use Rekalogika\File\Derivation\Filter\AbstractFileFilter;

final class ImageResizer extends AbstractFileFilter
{
    // vars

    private readonly ImageManager $manager;

    //
    // constants
    //

    public const ASPECTRATIO_ORIGINAL = 'original';

    public const ASPECTRATIO_SQUARE = 'square';

    //
    // constructor
    //

    public function __construct(
        ?ImageManager $manager = null,
        private readonly ?LoggerInterface $logger = null,
    ) {
        $this->manager = $manager ?? self::createGdManager();
    }

    /**
     * Constructs a manager bound to the GD driver. Using the constructor
     * directly (rather than the v3-only `ImageManager::gd()` static factory)
     * keeps this compatible with both Intervention Image v3 and v4.
     */
    private static function createGdManager(): ImageManager
    {
        return new ImageManager(new GdDriver());
    }

    //
    // properties
    //

    private int $maxWidthOrHeight = 512;

    /**
     * @var self::ASPECTRATIO_*
     */
    private string $aspect = self::ASPECTRATIO_ORIGINAL;

    //
    // action setters
    //

    /**
     * @param self::ASPECTRATIO_* $aspect
     */
    public function resize(
        int $maxWidthOrHeight = 512,
        string $aspect = self::ASPECTRATIO_ORIGINAL,
    ): self {
        $this->maxWidthOrHeight = $maxWidthOrHeight;
        $this->aspect = $aspect;

        return $this;
    }

    public function createThumbnail(): self
    {
        return $this->resize(512, self::ASPECTRATIO_ORIGINAL);
    }

    //
    // implementations
    //

    #[\Override]
    protected function getDerivationId(): string
    {
        return \sprintf('%s-%s', $this->aspect, $this->maxWidthOrHeight);
    }

    #[\Override]
    protected function process(): FileInterface
    {
        $width = $this->maxWidthOrHeight;
        $height = $this->maxWidthOrHeight;

        try {
            $ratio = null;

            $img = $this->decodeStream(
                $this->getSourceFile()->getContentAsStream()->detach(),
            );

            $w = $img->width();
            $h = $img->height();

            if ($this->aspect === self::ASPECTRATIO_SQUARE) {
                if ($w > $h) {
                    $img->crop($h, $h);
                } else {
                    $img->crop($w, $w);
                }

                $ratio = 1;
            } else {
                $ratio = $w / $h;
            }

            if ($width / $height > $ratio) {
                $width = (int) round((float) $height * (float) $ratio);
            } else {
                $height = (int) round((float) $width / (float) $ratio);
            }

            $img->resize($width, $height);
            $encoded = $img->encode(new AutoEncoder());
        } catch (\Throwable $e) {
            // log the exception
            $this->logger?->error($e->getMessage(), ['exception' => $e]);

            // output gray image
            $mimeType = $this->getSourceFile()->getType()->getName();

            $img = $this->createBlankImage($width, $height);

            try {
                $encoded = $img->encode(new AutoEncoder($mimeType));
            } catch (EncoderException | NotSupportedException) {
                // EncoderException covers driver-level failures; NotSupportedException
                // fires when AutoEncoder can't resolve the source's media type
                // (e.g. corrupt input reported as application/octet-stream).
                $encoded = $img->encode(new MediaTypeEncoder('image/png'));
            }
        }

        return $this->getFileRepository()
            ->createFromString(
                $this->getDerivationFilePointer(),
                $encoded->toString(),
            );
    }

    private function createBlankImage(int $width, int $height): ImageInterface
    {
        $manager = self::createGdManager();

        if (method_exists($manager, 'create')) {
            return $manager->create($width, $height)->fill('808080');
        }

        return $manager->createImage($width, $height)->fill('808080');
    }

    /**
     * Cross-version decoder: v3 exposes `read()` for a stream resource; v4
     * routes the same input through `decode()`/`decodeStream()`.
     */
    private function decodeStream(mixed $stream): ImageInterface
    {
        if (method_exists($this->manager, 'read')) {
            return $this->manager->read($stream);
        }

        return $this->manager->decode($stream);
    }
}
