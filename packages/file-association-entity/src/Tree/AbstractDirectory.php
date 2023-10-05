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

namespace Rekalogika\Domain\File\Association\Entity\Tree;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Rekalogika\Collections\Decorator\DxTrait\ArrayAccessDecoratorDxTrait;
use Rekalogika\Collections\Decorator\Trait\CollectionDecoratorTrait;
use Rekalogika\Contracts\File\FileNameInterface;
use Rekalogika\Contracts\File\Tree\DirectoryInterface;
use Rekalogika\Domain\File\Metadata\Model\FileName;

/**
 * Directory in a tree
 *
 * @implements Collection<int,AbstractNode>
 * @implements \IteratorAggregate<int,AbstractNode>
 */
class AbstractDirectory extends AbstractNode implements
    DirectoryInterface,
    Collection,
    \IteratorAggregate
{
    /**
     * @use CollectionDecoratorTrait<int,AbstractNode>
     * @use ArrayAccessDecoratorDxTrait<int,AbstractNode>
     */
    use CollectionDecoratorTrait, ArrayAccessDecoratorDxTrait {
        ArrayAccessDecoratorDxTrait::offsetExists insteadof CollectionDecoratorTrait;
        ArrayAccessDecoratorDxTrait::offsetGet insteadof CollectionDecoratorTrait;
        ArrayAccessDecoratorDxTrait::offsetSet insteadof CollectionDecoratorTrait;
        ArrayAccessDecoratorDxTrait::offsetUnset insteadof CollectionDecoratorTrait;
    }

    private ?string $name = null;

    /**
     * @var Collection<int,AbstractNode>
     */
    private Collection $entries;

    public function __construct()
    {
        $this->entries = new ArrayCollection();
    }

    /**
     * @return Collection<int,AbstractNode>
     */
    protected function getWrapped(): Collection
    {
        return $this->entries;
    }

    public function getName(): FileNameInterface
    {
        return new FileName($this->name);
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getDirectory(): ?DirectoryInterface
    {
        return $this->directory;
    }

    public function setDirectory(?AbstractDirectory $directory): void
    {
        $this->directory = $directory;
    }
}
