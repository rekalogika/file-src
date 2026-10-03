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
use Rekalogika\Contracts\File\DirectoryInterface;
use Rekalogika\Contracts\File\FileInterface;
use Rekalogika\Contracts\File\FileNameInterface;
use Rekalogika\Domain\File\Association\Entity\Internal\AbstractReadableCollectionDecorator;
use Rekalogika\Domain\File\Metadata\Model\FileName;
use Rekalogika\Domain\File\Metadata\Model\TranslatableFileName;
use Symfony\Contracts\Translation\TranslatableInterface;

/**
 * Decorates a ReadableCollection<FileInterface> so that it will also be an
 * instance of DirectoryInterface. The caller will be able to easily know that
 * the collection contains files. Designed to be used inside Doctrine entities.
 *
 * @template TKey of array-key
 * @template T of FileInterface
 * @extends AbstractReadableCollectionDecorator<TKey,T>
 * @implements DirectoryInterface<TKey,T>
 */
final class ReadableFileCollection extends AbstractReadableCollectionDecorator implements DirectoryInterface
{
    /**
     * @param ReadableCollection<TKey,T> $files
     */
    public function __construct(
        private readonly ReadableCollection $files,
        private readonly null|string|(TranslatableInterface&\Stringable) $name = null,
    ) {}

    /**
     * @return ReadableCollection<TKey,T>
     */
    #[\Override]
    protected function getWrapped(): ReadableCollection
    {
        return $this->files;
    }

    #[\Override]
    public function getName(): FileNameInterface
    {
        if ($this->name instanceof TranslatableInterface) {
            return new TranslatableFileName($this->name);
        }

        return new FileName($this->name);

    }
}
