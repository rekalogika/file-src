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

namespace Rekalogika\Domain\File\Association\Entity;

use Rekalogika\Contracts\File\Exception\MetadataNotFoundException;
use Rekalogika\Contracts\File\RawMetadataInterface;
use Rekalogika\Domain\File\Metadata\Constants;

/**
 * A Doctrine embeddable designed to store file metadata inside entities.
 *
 * @implements \IteratorAggregate<string,int|string|bool|null>
 */
class EmbeddedMetadata implements RawMetadataInterface, \IteratorAggregate
{
    private ?string $name = null;
    private ?int $size = null;
    private ?string $type = null;
    private ?\DateTimeInterface $modificationTime = null;
    private ?int $width = null;
    private ?int $height = null;

    /**
     * @var array<string,int|string|bool|null>
     */
    private array $other = [];

    /**
     * Indicates if the file is present. It is assumed that a file is present
     * if the type is not null. It should be at least application/octet-stream
     * if the file exists.
     */
    public function isFilePresent(): bool
    {
        return $this->type !== null;
    }

    public function clear(): void
    {
        $this->name = null;
        $this->size = null;
        $this->type = null;
        $this->modificationTime = null;
        $this->width = null;
        $this->height = null;
        $this->other = [];
    }

    public function getIterator(): \Traversable
    {
        yield Constants::FILE_NAME => $this->name;
        yield Constants::FILE_SIZE => $this->size;
        yield Constants::FILE_TYPE => $this->type;
        yield Constants::FILE_MODIFICATION_TIME => $this->modificationTime
            ? $this->modificationTime->getTimestamp()
            : null;
        yield Constants::MEDIA_WIDTH => $this->width;
        yield Constants::MEDIA_HEIGHT => $this->height;

        yield from $this->other;
    }

    public function get(string $key): int|string|bool|null
    {
        return match ($key) {
            Constants::FILE_NAME => $this->name,
            Constants::FILE_SIZE => $this->size,
            Constants::FILE_TYPE => $this->type,
            Constants::FILE_MODIFICATION_TIME => $this->modificationTime
                ? $this->modificationTime->getTimestamp()
                : null,
            Constants::MEDIA_WIDTH => $this->width,
            Constants::MEDIA_HEIGHT => $this->height,
            default => $this->other[$key] ?? throw new MetadataNotFoundException($key),
        };
    }

    public function tryGet(string $key): int|string|bool|null
    {
        try {
            return $this->get($key);
        } catch (MetadataNotFoundException) {
            return null;
        }
    }

    public function set(string $key, int|string|bool|null $value): void
    {
        match ($key) {
            Constants::FILE_NAME => $this->name = $value !== null ? (string) $value : null,
            Constants::FILE_SIZE => $this->size = (int) $value,
            Constants::FILE_TYPE => $this->type = $value !== null ? (string) $value : null,
            Constants::FILE_MODIFICATION_TIME => $this->modificationTime = $value
                ? new \DateTimeImmutable('@' . $value)
                : null,
            Constants::MEDIA_WIDTH => $this->width = $value !== null ? (int) $value : null,
            Constants::MEDIA_HEIGHT => $this->height = $value !== null ? (int) $value : null,
            default => $this->other[$key] = $value,
        };
    }

    public function delete(string $key): void
    {
        match ($key) {
            Constants::FILE_NAME => $this->name = null,
            Constants::FILE_SIZE => $this->size = null,
            Constants::FILE_TYPE => $this->type = null,
            Constants::FILE_MODIFICATION_TIME => $this->modificationTime = null,
            Constants::MEDIA_WIDTH => $this->width = null,
            Constants::MEDIA_HEIGHT => $this->height = null,
            default => $this->unset($key)
        };
    }

    private function unset(string $key): void
    {
        unset($this->other[$key]);
    }

    public function merge(iterable $metadata): void
    {
        foreach ($metadata as $key => $value) {
            $this->set($key, $value);
        }
    }

    public function count(): int
    {
        return count($this->other) + 6;
    }
}
