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

namespace Rekalogika\Domain\File\Metadata\Metadata;

use Rekalogika\Contracts\File\Metadata\ImageMetadataInterface;
use Rekalogika\Contracts\File\RawMetadataInterface;
use Rekalogika\Domain\File\Metadata\Constants;

final class ImageMetadata extends AbstractMetadata implements
    ImageMetadataInterface
{
    public static function create(
        RawMetadataInterface $metadata
    ): ?static {
        if (!$metadata->tryGet(Constants::MEDIA_WIDTH)) {
            return null;
        }

        return new static($metadata);
    }

    private function __construct(
        private RawMetadataInterface $metadata
    ) {
    }

    public function getWidth(): int
    {
        return (int) $this->metadata->tryGet(Constants::MEDIA_WIDTH);
    }

    public function getHeight(): int
    {
        return (int) $this->metadata->tryGet(Constants::MEDIA_HEIGHT);
    }

    public function getAspectRatio(): float
    {
        if ($this->getHeight() === 0) {
            return 0;
        }

        return $this->getWidth() / $this->getHeight();
    }

    public function isAspectRatio(float $aspectRatio): bool
    {
        return abs($this->getAspectRatio() - $aspectRatio) < 0.001;
    }

    public function isLandscape(): bool
    {
        return $this->getAspectRatio() > 1;
    }

    public function isPortrait(): bool
    {
        return $this->getAspectRatio() < 1;
    }

    public function isSquare(): bool
    {
        return $this->isAspectRatio(1);
    }
}
