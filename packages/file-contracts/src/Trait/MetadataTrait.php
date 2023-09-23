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

namespace Rekalogika\Contracts\File\Trait;

use Rekalogika\Contracts\File\FileMetadataInterface;
use Rekalogika\Contracts\File\FileNameInterface;
use Rekalogika\Contracts\File\FileTypeInterface;

trait MetadataTrait
{
    public function getName(): FileNameInterface
    {
        /** @var FileMetadataInterface */
        $metadata = $this->get(FileMetadataInterface::class);

        return $metadata->getName();
    }

    public function setName(?string $fileName): void
    {
        /** @var FileMetadataInterface */
        $metadata = $this->get(FileMetadataInterface::class);
        $metadata->setName($fileName);
        $this->flush();
    }

    public function getType(): FileTypeInterface
    {
        /** @var FileMetadataInterface */
        $metadata = $this->get(FileMetadataInterface::class);

        return $metadata->getType();
    }

    public function setType(string $type): void
    {
        /** @var FileMetadataInterface */
        $metadata = $this->get(FileMetadataInterface::class);
        $metadata->setType($type);
        $this->flush();
    }

    /**
     * @return int<0,max>
     */
    public function getSize(): int
    {
        /** @var FileMetadataInterface */
        $metadata = $this->get(FileMetadataInterface::class);

        return $metadata->getSize();
    }

    public function getLastModified(): \DateTimeInterface
    {
        /** @var FileMetadataInterface */
        $metadata = $this->get(FileMetadataInterface::class);

        return $metadata->getModificationTime();
    }
}
