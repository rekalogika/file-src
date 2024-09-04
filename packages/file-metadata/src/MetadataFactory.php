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

namespace Rekalogika\Domain\File\Metadata;

use Rekalogika\Contracts\File\FileMetadataInterface;
use Rekalogika\Contracts\File\Metadata\HttpMetadataInterface;
use Rekalogika\Contracts\File\Metadata\ImageMetadataInterface;
use Rekalogika\Contracts\File\RawMetadataInterface;
use Rekalogika\Domain\File\Metadata\Metadata\AbstractMetadata;
use Rekalogika\Domain\File\Metadata\Metadata\FileMetadata;
use Rekalogika\Domain\File\Metadata\Metadata\HttpMetadata;
use Rekalogika\Domain\File\Metadata\Metadata\ImageMetadata;

final class MetadataFactory
{
    public static function create(RawMetadataInterface $metadata): self
    {
        return new self($metadata);
    }

    private function __construct(
        private readonly RawMetadataInterface $metadata,
    ) {}

    /**
     * @template T of AbstractMetadata
     * @param string|class-string<T> $id
     * @return ($id is class-string<T> ? class-string<T> : class-string<AbstractMetadata>|null)
     */
    private function getMetadataClass(string $id): ?string
    {
        switch ($id) {
            case HttpMetadataInterface::class:
            case 'httpMetadata':
                return HttpMetadata::class;

            case FileMetadataInterface::class:
            case 'fileMetadata':
                return FileMetadata::class;

            case ImageMetadataInterface::class:
            case 'imageMetadata':
                return ImageMetadata::class;
        }

        return null;
    }

    /**
     * @template T of object
     * @param class-string<T>|string $id
     * @return ($id is class-string<T> ? T : mixed)
     */
    public function get(string $id)
    {
        switch ($id) {
            case RawMetadataInterface::class:
            case 'rawMetadata':
                return $this->metadata;

            default:
                $class = $this->getMetadataClass($id);
                if ($class === null) {
                    return null;
                }

                return $class::create($this->metadata);
        }
    }
}
