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

namespace Rekalogika\Contracts\File\Metadata;

/**
 * Represent metadata of an image file.
 */
interface ImageMetadataInterface
{
    public function getWidth(): int;

    public function getHeight(): int;

    public function getAspectRatio(): float;

    public function isAspectRatio(float $aspectRatio): bool;

    public function isLandscape(): bool;

    public function isPortrait(): bool;

    public function isSquare(): bool;
}
