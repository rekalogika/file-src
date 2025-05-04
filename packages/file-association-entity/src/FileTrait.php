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

use Doctrine\ORM\Mapping\Embedded;
use Rekalogika\Contracts\File\FileInterface;
use Rekalogika\Contracts\File\Trait\FileDecoratorTrait;
use Rekalogika\Domain\File\Null\NullFile;
use Rekalogika\File\Association\Attribute\AsFileAssociation;
use Rekalogika\File\Association\Model\FetchMode;

/**
 * Trait to help the creation of a file entity (an entity implementing
 * FileInterface). Classes using this trait are expected to:
 *
 * 1. Implement FileInterface
 * 2. Call setWrapped in the constructor to inject the real file
 * 3. Add `#[WithFileAssociation]` attribute to the class.
 */
trait FileTrait
{
    use FileDecoratorTrait;

    #[AsFileAssociation(FetchMode::Lazy)]
    private FileInterface $file;

    #[Embedded()]
    private ?EmbeddedMetadata $metadata = null;

    private function getMetadata(): EmbeddedMetadata
    {
        return $this->metadata ??= new EmbeddedMetadata();
    }

    private function setWrapped(FileInterface $file): void
    {
        $this->file = new NullFile(); // needs this for the next line to work
        FileDecorator::setFileMandatory($file, $this->file, $this->getMetadata());
    }

    private function getWrapped(): FileInterface
    {
        if (!isset($this->file)) {
            $this->file = new UnsetFile(static::class, 'file');
        }

        return FileDecorator::getFile($this->file, $this->getMetadata()) ?? new NullFile();
    }
}
