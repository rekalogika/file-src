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
use Rekalogika\Collections\Decorator\Decorator\CollectionDecorator;
use Rekalogika\Contracts\File\DirectoryInterface;
use Rekalogika\Contracts\File\FileInterface;
use Rekalogika\Contracts\File\FileNameInterface;
use Rekalogika\Domain\File\Metadata\Model\FileName;
use Rekalogika\Domain\File\Metadata\Model\TranslatableFileName;
use Symfony\Contracts\Translation\TranslatableInterface;

/**
 * Decorates a Collection<FileInterface> so that it will also be an instance of
 * DirectoryInterface. The caller will be able to easily know that the
 * collection contains files. Designed to be used inside Doctrine entities.
 *
 * @template TKey of array-key
 * @template T of FileInterface
 * @extends CollectionDecorator<TKey,T>
 * @implements DirectoryInterface<TKey,T>
 */
final class FileCollection extends CollectionDecorator implements DirectoryInterface
{
    /**
     * @param Collection<TKey,T> $files
     */
    public function __construct(
        Collection $files,
        private readonly null|string|(TranslatableInterface&\Stringable) $name = null,
    ) {
        parent::__construct($files);
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
