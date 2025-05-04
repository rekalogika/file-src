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

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class ImageTwigExtension extends AbstractExtension
{
    #[\Override]
    public function getFilters(): array
    {
        return [
            new TwigFilter(
                'image_resize',
                [ImageTwigRuntime::class, 'fileImageResize'],
            ),
        ];
    }
}
