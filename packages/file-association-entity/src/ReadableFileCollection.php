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

use Doctrine\Common\Collections\ReadableCollection;
use Rekalogika\Collections\Decorator\Trait\ReadableCollectionDecoratorTrait;
use Rekalogika\Contracts\File\DirectoryInterface;
use Rekalogika\Contracts\File\FileInterface;
use Rekalogika\Contracts\File\FileNameInterface;
use Rekalogika\Domain\File\Metadata\Model\FileName;

/**
 * Decorates a ReadableCollection<FileInterface> so that it will also be an
 * instance of DirectoryInterface. The caller will be able to easily know that
 * the collection contains files. Designed to be used inside Doctrine entities.
 *
 * @template TKey of array-key
 * @template T of FileInterface
 * @implements ReadableCollection<TKey,T>
 * @implements DirectoryInterface<TKey,T>
 */
final class ReadableFileCollection implements ReadableCollection, DirectoryInterface
{
    /**
     * @use ReadableCollectionDecoratorTrait<TKey,T>
     */
    use ReadableCollectionDecoratorTrait;

    /**
     * @param ReadableCollection<TKey,T> $files
     */
    public function __construct(
        private ReadableCollection $files,
        private ?string $name = null
    ) {
    }

    /**
     * @return ReadableCollection<TKey,T>
     */
    protected function getWrapped(): ReadableCollection
    {
        return $this->files;
    }

    public function getName(): FileNameInterface
    {
        return new FileName($this->name);
    }
}
