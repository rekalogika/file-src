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

namespace Rekalogika\File\Metadata;

use Rekalogika\Contracts\File\FileInterface;
use Rekalogika\Contracts\File\FileMetadataInterface;
use Rekalogika\Contracts\File\Metadata\HttpMetadataInterface;
use Rekalogika\Contracts\File\Metadata\ImageMetadataInterface;
use Rekalogika\Contracts\File\RawMetadataInterface;

final class MetadataFactory
{
    public function __construct(
        private FileInterface $file,
        private RawMetadataInterface $metadata
    ) {
    }

    /**
     * @template T of AbstractMetadata
     * @param string|class-string<T> $id
     * @return ($id is class-string<T> ? T : class-string<AbstractMetadata>|null)
     */
    private static function getMetadataClass(string $id)
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
                $class = self::getMetadataClass($id);
                if ($class === null) {
                    return null;
                }

                return $class::create($this->file, $this->metadata);
        }
    }
}
