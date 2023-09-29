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
use Rekalogika\Contracts\File\Trait\FileDecoratorTrait;
use Rekalogika\File\Association\Attribute\AsFileAssociation;
use Rekalogika\File\Association\Attribute\WithFileAssociation;

#[WithFileAssociation]
abstract class AbstractFile implements FileInterface
{
    use FileDecoratorTrait;

    #[AsFileAssociation]
    private ?FileInterface $file = null;

    private EmbeddedMetadata $metadata;

    public function __construct(
        FileInterface $file,
    ) {
        $this->file = $file;
        $this->metadata = new EmbeddedMetadata();
        FileDecorator::setFile($file, $this->file, $this->metadata);
    }

    protected function getWrapped(): FileInterface
    {
        return FileDecorator::getFile($this->file, $this->metadata) ?? new NullFile();
    }
}
