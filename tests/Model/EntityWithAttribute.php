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

use Rekalogika\Contracts\File\FileInterface;
use Rekalogika\File\Association\Attribute\AsFileAssociation;

class EntityWithAttribute
{
    #[AsFileAssociation]
    private ?FileInterface $file = null;

    private ?FileInterface $nonAssociatedFile = null;

    public function __construct(
        private readonly string $id,
    ) {}

    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @phpstan-impure
     */
    public function getFile(): ?FileInterface
    {
        return $this->file;
    }

    public function setFile(?FileInterface $file): self
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get the value of nonAssociatedFile
     */
    public function getNonAssociatedFile(): ?FileInterface
    {
        return $this->nonAssociatedFile;
    }

    /**
     * Set the value of nonAssociatedFile
     */
    public function setNonAssociatedFile(?FileInterface $nonAssociatedFile): self
    {
        $this->nonAssociatedFile = $nonAssociatedFile;

        return $this;
    }
}
