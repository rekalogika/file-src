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

use Rekalogika\Contracts\File\FileInterface;
use Rekalogika\File\Association\Attribute\WithFileAssociation;

/**
 * Easily create a Doctrine entity that implements FileInterface.
 */
#[WithFileAssociation]
abstract class AbstractFile implements FileInterface
{
    use FileTrait;

    public function __construct(
        FileInterface $file,
    ) {
        $this->setWrapped($file);
    }
}
