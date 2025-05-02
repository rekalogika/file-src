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
use Rekalogika\File\Association\Attribute\AsFileAssociation;
use Rekalogika\File\Association\Attribute\WithFileAssociation;
use Rekalogika\File\Association\Model\FetchMode;

#[WithFileAssociation]
final class EntityWithLazyFile
{
    #[AsFileAssociation(fetch: FetchMode::Lazy)]
    private ?FileInterface $file = null;

    public function __construct(private readonly string $id) {}

    /**
     * Get the value of file
     */
    public function getFile(): ?FileInterface
    {
        return $this->file;
    }

    /**
     * Set the value of file
     */
    public function setFile(?FileInterface $file): self
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get the value of id
     */
    public function getId(): string
    {
        return $this->id;
    }
}
