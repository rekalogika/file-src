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

use Rekalogika\Contracts\File\FileInterface;
use Twig\Extension\RuntimeExtensionInterface;

final class ImageTwigRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private ImageResizer $imageResizer,
    ) {}

    /**
     * @param ImageResizer::ASPECTRATIO_* $aspect
     */
    public function fileImageResize(
        ?FileInterface $file,
        int $maxWidthOrHeight = 512,
        string $aspect = ImageResizer::ASPECTRATIO_ORIGINAL,
    ): ?FileInterface {
        if ($file === null) {
            return null;
        }

        return $this->imageResizer
            ->take($file)
            ->resize($maxWidthOrHeight, $aspect)
            ->getResult();
    }

    /**
     * Renders the file inline as a data URI. Unlike a temporary URL, this does
     * not require the file to still exist when the browser dereferences it,
     * which makes it the only way to preview a file that is not persisted, and
     * therefore will be gone by the next request.
     */
    public function fileDataUri(?FileInterface $file): ?string
    {
        if ($file === null) {
            return null;
        }

        return \sprintf(
            'data:%s;base64,%s',
            $file->getType()->getName(),
            base64_encode($file->getContent()),
        );
    }
}
