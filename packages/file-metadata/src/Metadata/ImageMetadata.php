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
    #[\Override]
    public static function create(
        RawMetadataInterface $metadata,
    ): ?static {
        if (!((bool) $metadata->tryGet(Constants::MEDIA_WIDTH))) {
            return null;
        }

        return new self($metadata);
    }

    private function __construct(
        private readonly RawMetadataInterface $metadata,
    ) {}

    #[\Override]
    public function getWidth(): int
    {
        return (int) $this->metadata->tryGet(Constants::MEDIA_WIDTH);
    }

    #[\Override]
    public function getHeight(): int
    {
        return (int) $this->metadata->tryGet(Constants::MEDIA_HEIGHT);
    }

    #[\Override]
    public function getAspectRatio(): float
    {
        if ($this->getHeight() === 0) {
            return 0;
        }

        return $this->getWidth() / $this->getHeight();
    }

    #[\Override]
    public function isAspectRatio(float $aspectRatio): bool
    {
        return abs($this->getAspectRatio() - $aspectRatio) < 0.001;
    }

    #[\Override]
    public function isLandscape(): bool
    {
        return $this->getAspectRatio() > 1;
    }

    #[\Override]
    public function isPortrait(): bool
    {
        return $this->getAspectRatio() < 1;
    }

    #[\Override]
    public function isSquare(): bool
    {
        return $this->isAspectRatio(1);
    }
}
