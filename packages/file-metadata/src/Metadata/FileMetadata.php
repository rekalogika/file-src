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

use Rekalogika\Contracts\File\FileMetadataInterface;
use Rekalogika\Contracts\File\FileNameInterface;
use Rekalogika\Contracts\File\FileTypeInterface;
use Rekalogika\Contracts\File\RawMetadataInterface;
use Rekalogika\Domain\File\Metadata\Constants;
use Rekalogika\Domain\File\Metadata\Model\FileName;
use Rekalogika\Domain\File\Metadata\Model\MimeMapFileTypeAdapter;

final class FileMetadata extends AbstractMetadata implements FileMetadataInterface
{
    public static function create(
        RawMetadataInterface $metadata
    ): static {
        return new static($metadata);
    }

    private function __construct(
        private RawMetadataInterface $metadata
    ) {
    }

    public function getName(): FileNameInterface
    {
        $result = $this->metadata->tryGet(Constants::FILE_NAME);

        if ($result === null) {
            return new FileName(null, $this->getType()->getExtension());
        }
        return new FileName((string) $result);
    }

    public function setName(?string $fileName): void
    {
        if (null === $fileName) {
            $this->metadata->delete(Constants::FILE_NAME);
            return;
        }

        $extension = pathinfo($fileName, PATHINFO_EXTENSION);

        if (!$extension) {
            $type = $this->getType();
            $extension = $type->getExtension();
            if ($extension !== null) {
                $fileName .= '.' . $extension;
            }
        }

        $this->metadata->set(Constants::FILE_NAME, $fileName);
    }

    public function getSize(): int
    {
        $size = $this->metadata->tryGet(Constants::FILE_SIZE) ?? 0;

        if (!is_int($size)) {
            $size = (int) $size;
        }

        if ($size < 0) {
            $size = 0;
        }

        return $size;
    }

    public function getType(): FileTypeInterface
    {
        $type = (string) ($this->metadata->tryGet(Constants::FILE_TYPE)
            ?? 'application/octet-stream');

        return new MimeMapFileTypeAdapter($type);
    }

    public function setType(string $type): void
    {
        $this->metadata->set(Constants::FILE_TYPE, $type);
    }

    public function getModificationTime(): \DateTimeInterface
    {
        $result = $this->metadata->tryGet(Constants::FILE_MODIFICATION_TIME);

        // if the metadata is not set, we set it to the current time, and return it
        if ($result === null) {
            $modificationTime = new \DateTimeImmutable();
            $this->metadata->set(
                Constants::FILE_MODIFICATION_TIME,
                $modificationTime->getTimestamp()
            );

            return $modificationTime;
        }

        $result = \DateTimeImmutable::createFromFormat('U', (string) $result);

        if ($result === false) {
            $modificationTime = new \DateTimeImmutable();
            $this->metadata->set(
                Constants::FILE_MODIFICATION_TIME,
                $modificationTime->getTimestamp()
            );

            return $modificationTime;
        }

        return $result;
    }
}
