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
use Rekalogika\Contracts\File\DirectoryInterface;
use Rekalogika\Contracts\File\FileInterface;
use Rekalogika\Contracts\File\FileNameInterface;
use Rekalogika\Domain\File\Association\Entity\Internal\AbstractReadableCollectionDecorator;
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
 * @extends AbstractReadableCollectionDecorator<TKey,T>
 * @implements Collection<TKey,T>
 * @implements DirectoryInterface<TKey,T>
 */
final class FileCollection extends AbstractReadableCollectionDecorator implements Collection, DirectoryInterface
{
    /**
     * @param Collection<TKey,T> $files
     */
    public function __construct(
        private readonly Collection $files,
        private readonly null|string|(TranslatableInterface&\Stringable) $name = null,
    ) {}

    /**
     * @return Collection<TKey,T>
     */
    #[\Override]
    protected function getWrapped(): Collection
    {
        return $this->files;
    }

    /**
     * @param T $element
     */
    #[\Override]
    public function add(mixed $element): void
    {
        $this->files->add($element);
    }

    #[\Override]
    public function clear(): void
    {
        $this->files->clear();
    }

    /**
     * @param TKey $key
     * @return T|null
     */
    #[\Override]
    public function remove(string|int $key): mixed
    {
        return $this->files->remove($key);
    }

    /**
     * @param T $element
     */
    #[\Override]
    public function removeElement(mixed $element): bool
    {
        return $this->files->removeElement($element);
    }

    /**
     * @param TKey $key
     * @param T $value
     */
    #[\Override]
    public function set(string|int $key, mixed $value): void
    {
        $this->files->set($key, $value);
    }

    /**
     * @param \Closure(T,TKey):bool $p
     * @return Collection<TKey,T>
     */
    #[\Override]
    public function filter(\Closure $p): Collection
    {
        return $this->files->filter($p);
    }

    /**
     * @template U
     * @param \Closure(T):U $func
     * @return Collection<TKey,U>
     */
    #[\Override]
    public function map(\Closure $func): Collection
    {
        return $this->files->map($func);
    }

    /**
     * @param \Closure(TKey,T):bool $p
     * @return array{0:Collection<TKey,T>,1:Collection<TKey,T>}
     */
    #[\Override]
    public function partition(\Closure $p): array
    {
        return $this->files->partition($p);
    }

    /**
     * @param TKey $offset
     */
    #[\Override]
    public function offsetExists(mixed $offset): bool
    {
        return $this->files->offsetExists($offset);
    }

    /**
     * @param TKey $offset
     * @return T|null
     */
    #[\Override]
    public function offsetGet(mixed $offset): mixed
    {
        return $this->files->offsetGet($offset);
    }

    /**
     * @param TKey|null $offset
     * @param T $value
     */
    #[\Override]
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->files->offsetSet($offset, $value);
    }

    /**
     * @param TKey $offset
     */
    #[\Override]
    public function offsetUnset(mixed $offset): void
    {
        $this->files->offsetUnset($offset);
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
