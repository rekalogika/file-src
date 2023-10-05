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

use Doctrine\Common\Collections\Collection;
use Rekalogika\Collections\Decorator\Trait\CollectionDecoratorTrait;
use Rekalogika\Contracts\File\FileInterface;
use Rekalogika\Contracts\File\FilesInterface;

/**
 * Decorates a Collection<FileInterface> to be a FilesInterface, so the caller
 * can easily know that the collection contains files.
 *
 * @implements Collection<array-key,FileInterface>
 */
final class FilesDecorator implements Collection, FilesInterface
{
    /**
     * @use CollectionDecoratorTrait<array-key,FileInterface>
     */
    use CollectionDecoratorTrait;

    /**
     * @param Collection<array-key,FileInterface> $files
     */
    public function __construct(private Collection $files)
    {
    }

    /**
     * @return Collection<array-key,FileInterface>
     */
    protected function getWrapped(): Collection
    {
        return $this->files;
    }
}
