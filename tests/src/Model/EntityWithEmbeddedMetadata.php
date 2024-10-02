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

namespace Rekalogika\File\Tests\Model;

use Doctrine\ORM\Mapping\Embedded;
use Doctrine\ORM\Mapping\Entity;
use Rekalogika\Contracts\File\FileInterface;
use Rekalogika\Contracts\File\RawMetadataInterface;
use Rekalogika\Domain\File\Association\Entity\EmbeddedMetadata;
use Rekalogika\Domain\File\Association\Entity\FileDecorator;

#[Entity()]
class EntityWithEmbeddedMetadata
{
    private ?FileInterface $file = null;

    #[Embedded()]
    private EmbeddedMetadata $fileMetadata;

    public function __construct(
        private string $id,
    ) {
        $this->fileMetadata = new EmbeddedMetadata();
    }

    /**
     * Get the value of file
     */
    public function getFile(): ?FileInterface
    {
        return FileDecorator::getFile($this->file, $this->fileMetadata);
    }

    /**
     * Set the value of file
     */
    public function setFile(?FileInterface $file): self
    {
        FileDecorator::setFile($file, $this->file, $this->fileMetadata);

        return $this;
    }

    /**
     * Get the value of fileMetadata
     */
    public function getFileMetadata(): RawMetadataInterface
    {
        return $this->fileMetadata;
    }

    /**
     * Get the value of id
     */
    public function getId(): string
    {
        return $this->id;
    }
}
