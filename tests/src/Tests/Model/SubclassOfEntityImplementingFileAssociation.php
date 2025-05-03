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

namespace Rekalogika\File\Tests\Tests\Model;

use Rekalogika\Contracts\File\FileInterface;

final class SubclassOfEntityImplementingFileAssociation extends EntityImplementingFileAssociation
{
    #[\Override]
    public static function getFileAssociationPropertyList(): array
    {
        return [
            'file',
            'protectedFile',
            'anotherFile',
        ];
    }

    private ?FileInterface $anotherFile = null;

    /**
     * Private property with the same name as the parent class.
     */
    private ?FileInterface $file = null;

    private ?FileInterface $unmanagedFile = null;

    protected ?FileInterface $protectedFile = null;

    /**
     * Get the value of anotherFile
     */
    public function getAnotherFile(): ?FileInterface
    {
        return $this->anotherFile;
    }

    /**
     * Set the value of anotherFile
     */
    public function setAnotherFile(?FileInterface $anotherFile): self
    {
        $this->anotherFile = $anotherFile;

        return $this;
    }

    /**
     * Get the value of unmanagedFile
     */
    public function getUnmanagedFile(): ?FileInterface
    {
        return $this->unmanagedFile;
    }

    /**
     * Set the value of unmanagedFile
     */
    public function setUnmanagedFile(?FileInterface $unmanagedFile): self
    {
        $this->unmanagedFile = $unmanagedFile;

        return $this;
    }

    /**
     * Get the value of file
     */
    #[\Override]
    public function getFile(): ?FileInterface
    {
        return $this->file;
    }

    /**
     * Set the value of file
     */
    #[\Override]
    public function setFile(?FileInterface $file): self
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get the value of protectedFile
     */
    #[\Override]
    public function getProtectedFile(): ?FileInterface
    {
        return $this->protectedFile;
    }

    /**
     * Set the value of protectedFile
     */
    #[\Override]
    public function setProtectedFile(?FileInterface $protectedFile): self
    {
        $this->protectedFile = $protectedFile;

        return $this;
    }
}
