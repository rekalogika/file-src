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

namespace Rekalogika\Domain\File\Association\Entity\Internal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ReadableCollection;
use Doctrine\Common\Collections\Selectable;

/**
 * Forwards all ReadableCollection & Selectable methods to the wrapped
 * collection.
 *
 * @template TKey of array-key
 * @template T
 * @implements ReadableCollection<TKey,T>
 * @implements Selectable<TKey,T>
 * @internal
 */
abstract class AbstractReadableCollectionDecorator implements
    ReadableCollection,
    Selectable
{
    /**
     * @return ReadableCollection<TKey,T>
     */
    abstract protected function getWrapped(): ReadableCollection;

    #[\Override]
    public function count(): int
    {
        return $this->getWrapped()->count();
    }

    /**
     * @return \Traversable<TKey,T>
     */
    #[\Override]
    public function getIterator(): \Traversable
    {
        return $this->getWrapped()->getIterator();
    }

    /**
     * @template TMaybeContained
     * @param TMaybeContained $element
     * @return (TMaybeContained is T ? bool : false)
     */
    #[\Override]
    public function contains(mixed $element): bool
    {
        return $this->getWrapped()->contains($element);
    }

    #[\Override]
    public function isEmpty(): bool
    {
        return $this->getWrapped()->isEmpty();
    }

    /**
     * @param TKey $key
     */
    #[\Override]
    public function containsKey(string|int $key): bool
    {
        return $this->getWrapped()->containsKey($key);
    }

    /**
     * @param TKey $key
     * @return T|null
     */
    #[\Override]
    public function get(string|int $key): mixed
    {
        return $this->getWrapped()->get($key);
    }

    /**
     * @return list<TKey>
     */
    #[\Override]
    public function getKeys(): array
    {
        return $this->getWrapped()->getKeys();
    }

    /**
     * @return list<T>
     */
    #[\Override]
    public function getValues(): array
    {
        return $this->getWrapped()->getValues();
    }

    /**
     * @return array<TKey,T>
     */
    #[\Override]
    public function toArray(): array
    {
        return $this->getWrapped()->toArray();
    }

    /**
     * @return T|false
     */
    #[\Override]
    public function first(): mixed
    {
        return $this->getWrapped()->first();
    }

    /**
     * @return T|false
     */
    #[\Override]
    public function last(): mixed
    {
        return $this->getWrapped()->last();
    }

    /**
     * @return TKey|null
     */
    #[\Override]
    public function key(): int|string|null
    {
        return $this->getWrapped()->key();
    }

    /**
     * @return T|false
     */
    #[\Override]
    public function current(): mixed
    {
        return $this->getWrapped()->current();
    }

    /**
     * @return T|false
     */
    #[\Override]
    public function next(): mixed
    {
        return $this->getWrapped()->next();
    }

    /**
     * @return array<TKey,T>
     */
    #[\Override]
    public function slice(int $offset, ?int $length = null): array
    {
        return $this->getWrapped()->slice($offset, $length);
    }

    /**
     * @param \Closure(TKey,T):bool $p
     */
    #[\Override]
    public function exists(\Closure $p): bool
    {
        return $this->getWrapped()->exists($p);
    }

    /**
     * @param \Closure(TKey,T):bool $p
     */
    #[\Override]
    public function forAll(\Closure $p): bool
    {
        return $this->getWrapped()->forAll($p);
    }

    /**
     * @template TMaybeContained
     * @param TMaybeContained $element
     * @return (TMaybeContained is T ? TKey|false : false)
     */
    #[\Override]
    public function indexOf(mixed $element): int|string|false
    {
        return $this->getWrapped()->indexOf($element);
    }

    /**
     * @param \Closure(TKey,T):bool $p
     * @return T|null
     */
    #[\Override]
    public function findFirst(\Closure $p): mixed
    {
        return $this->getWrapped()->findFirst($p);
    }

    /**
     * @template TReturn
     * @template TInitial
     * @param \Closure(TReturn|TInitial|null, T):(TInitial|TReturn) $func
     * @param TInitial|null $initial
     * @return TReturn|TInitial|null
     */
    #[\Override]
    public function reduce(\Closure $func, mixed $initial = null): mixed
    {
        return $this->getWrapped()->reduce($func, $initial);
    }

    /**
     * @param \Closure(T,TKey):bool $p
     * @return ReadableCollection<TKey,T>
     */
    #[\Override]
    public function filter(\Closure $p): ReadableCollection
    {
        return $this->getWrapped()->filter($p);
    }

    /**
     * @template U
     * @param \Closure(T):U $func
     * @return ReadableCollection<TKey,U>
     */
    #[\Override]
    public function map(\Closure $func): ReadableCollection
    {
        return $this->getWrapped()->map($func);
    }

    /**
     * @param \Closure(TKey,T):bool $p
     * @return array{0:ReadableCollection<TKey,T>,1:ReadableCollection<TKey,T>}
     */
    #[\Override]
    public function partition(\Closure $p): array
    {
        return $this->getWrapped()->partition($p);
    }

    /**
     * @return ReadableCollection<TKey,T>&Selectable<TKey,T>
     */
    #[\Override]
    public function matching(Criteria $criteria): ReadableCollection&Selectable
    {
        $wrapped = $this->getWrapped();

        if ($this->isSelectable($wrapped)) {
            return $wrapped->matching($criteria);
        }

        // doctrine/collections 2.x does not require a ReadableCollection to be
        // Selectable
        return (new ArrayCollection($wrapped->toArray()))->matching($criteria);
    }

    /**
     * A ReadableCollection that is also Selectable shares the same key and
     * value types in both interfaces.
     *
     * @param ReadableCollection<TKey,T> $collection
     * @phpstan-assert-if-true ReadableCollection<TKey,T>&Selectable<TKey,T> $collection
     * @psalm-assert-if-true ReadableCollection<TKey,T>&Selectable<TKey,T> $collection
     */
    private function isSelectable(ReadableCollection $collection): bool
    {
        return $collection instanceof Selectable;
    }
}
