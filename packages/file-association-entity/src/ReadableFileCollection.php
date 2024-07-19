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
use Rekalogika\Collections\Decorator\Decorator\ReadableCollectionDecorator;
use Rekalogika\Contracts\File\DirectoryInterface;
use Rekalogika\Contracts\File\FileInterface;
use Rekalogika\Contracts\File\FileNameInterface;
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
 * @extends ReadableCollectionDecorator<TKey,T>
 * @implements DirectoryInterface<TKey,T>
 */
final class ReadableFileCollection extends ReadableCollectionDecorator implements DirectoryInterface
{
    /**
     * @param ReadableCollection<TKey,T> $files
     */
    public function __construct(
        ReadableCollection $files,
        private readonly null|string|(TranslatableInterface&\Stringable) $name = null
    ) {
        parent::__construct($files);
    }

    public function getName(): FileNameInterface
    {
        if ($this->name instanceof TranslatableInterface) {
            return new TranslatableFileName($this->name);
        }
        return new FileName($this->name);

    }
}
