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

use Intervention\Image\Encoders\AutoEncoder;
use Intervention\Image\ImageManager;
use Rekalogika\Contracts\File\FileInterface;
use Rekalogika\File\Derivation\Filter\AbstractFileFilter;

class ImageResizer extends AbstractFileFilter
{
    //
    // constants
    //

    final public const ASPECTRATIO_ORIGINAL = 'original';
    final public const ASPECTRATIO_SQUARE = 'square';

    //
    // properties
    //

    private int $maxWidthOrHeight = 512;
    private string $aspect = self::ASPECTRATIO_ORIGINAL;

    //
    // action setters
    //

    public function resize(
        int $maxWidthOrHeight = 512,
        string $aspect = self::ASPECTRATIO_ORIGINAL
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

    protected function getDerivationId(): string
    {
        return sprintf('%s-%s', $this->aspect, $this->maxWidthOrHeight);
    }

    protected function process(): FileInterface
    {
        $ratio = null;

        $img = ImageManager::gd()
            ->read($this->getSourceFile()->getContentAsStream()->detach());

        $w = $img->width();
        $h = $img->height();

        if ($this->aspect == self::ASPECTRATIO_SQUARE) {
            if ($w > $h) {
                $img->crop($h, $h);
            } else {
                $img->crop($w, $w);
            }

            $ratio = 1;
        } elseif ($this->aspect == self::ASPECTRATIO_ORIGINAL) {
            $ratio = $w / $h;
        } else {
            throw new \InvalidArgumentException(sprintf(
                'Unknown aspect ratio "%s"',
                $this->aspect
            ));
        }

        $width = $this->maxWidthOrHeight;
        $height = $this->maxWidthOrHeight;

        if ($width / $height > $ratio) {
            $width = $height * $ratio;
        } else {
            $height = $width / $ratio;
        }

        $img->resize((int) round($width), (int) round($height));
        $encoded = $img->encode(new AutoEncoder());

        return $this->getFileRepository()
            ->createFromString(
                $this->getDerivationFilePointer(),
                $encoded->toString(),
            );
    }
}
