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

use Rekalogika\Contracts\File\Association\FileAssociationInterface;
use Rekalogika\Contracts\File\FileInterface;

class Entity implements FileAssociationInterface
{
    private ?FileInterface $file = null;

    public function __construct(
        private readonly string $id,
    ) {}

    #[\Override]
    public static function getFileAssociationPropertyList(): array
    {
        return ['file'];
    }

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
}
