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

class SubclassOfEntityWithAttribute extends EntityWithAttribute
{
    #[AsFileAssociation]
    private ?FileInterface $anotherFile = null;

    private ?FileInterface $unmanagedFile = null;

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
}
