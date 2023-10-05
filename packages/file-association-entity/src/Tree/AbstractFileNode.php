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

use Rekalogika\Contracts\File\FileInterface;
use Rekalogika\Contracts\File\Tree\DirectoryInterface;
use Rekalogika\Contracts\File\Tree\FileNodeInterface;
use Rekalogika\Domain\File\Association\Entity\FileTrait;
use Rekalogika\File\Association\Attribute\WithFileAssociation;

/**
 * File in a directory
 */
#[WithFileAssociation]
class AbstractFileNode extends AbstractNode implements FileNodeInterface
{
    use FileTrait;

    public function __construct(
        FileInterface $file,
    ) {
        $this->setWrapped($file);
    }

    public function getDirectory(): DirectoryInterface
    {
        if (null === $this->directory) {
            throw new \RuntimeException('Directory is not set');
        }

        return $this->directory;
    }

    public function setDirectory(AbstractDirectory $directory): void
    {
        $this->directory = $directory;
    }
}
