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
use Rekalogika\Contracts\File\FileNameInterface;
use Rekalogika\Contracts\File\DirectoryInterface;
use Rekalogika\Domain\File\Metadata\Model\FileName;

/**
 * Decorates a Collection<FileInterface> so that it will also be a
 * DirectoryInterface. The caller will be able to easily know that the
 * collection contains files. Designed to be used inside Doctrine entities.
 *
 * @template TKey of array-key
 * @template T of FileInterface
 * @implements Collection<TKey,T>
 * @implements DirectoryInterface<TKey,T>
 */
final class FileCollectionDecorator implements Collection, DirectoryInterface
{
    /**
     * @use CollectionDecoratorTrait<TKey,T>
     */
    use CollectionDecoratorTrait;

    /**
     * @param Collection<TKey,T> $files
     */
    public function __construct(
        private Collection $files,
        private ?string $name = null
    ) {
    }

    /**
     * @return Collection<TKey,T>
     */
    protected function getWrapped(): Collection
    {
        return $this->files;
    }

    public function getName(): FileNameInterface
    {
        return new FileName($this->name);
    }
}
