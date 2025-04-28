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

namespace Rekalogika\File\Tests\Tests\Model;

use Rekalogika\Contracts\File\FileInterface;
use Rekalogika\File\Association\Attribute\AsFileAssociation;

/** @psalm-suppress MissingConstructor */
final class EntityWithDifferentFileProperties
{
    #[AsFileAssociation(fetch: 'EAGER')]
    private ?FileInterface $mandatoryEager = null;

    #[AsFileAssociation(fetch: 'EAGER')]
    private FileInterface $notMandatoryEager;

    #[AsFileAssociation(fetch: 'LAZY')]
    private ?FileInterface $mandatoryLazy = null;

    #[AsFileAssociation(fetch: 'LAZY')]
    private FileInterface $notMandatoryLazy;

    #[AsFileAssociation]
    private \stdClass $nonFileProperty;

    private FileInterface $fileWithoutAttribute;

    public function getMandatoryEager(): ?FileInterface
    {
        return $this->mandatoryEager;
    }

    public function setMandatoryEager(?FileInterface $mandatoryEager): void
    {
        $this->mandatoryEager = $mandatoryEager;
    }

    public function getNotMandatoryEager(): FileInterface
    {
        return $this->notMandatoryEager;
    }

    public function setNotMandatoryEager(FileInterface $notMandatoryEager): void
    {
        $this->notMandatoryEager = $notMandatoryEager;
    }

    public function getMandatoryLazy(): ?FileInterface
    {
        return $this->mandatoryLazy;
    }

    public function setMandatoryLazy(?FileInterface $mandatoryLazy): void
    {
        $this->mandatoryLazy = $mandatoryLazy;
    }

    public function getNotMandatoryLazy(): FileInterface
    {
        return $this->notMandatoryLazy;
    }

    public function setNotMandatoryLazy(FileInterface $notMandatoryLazy): void
    {
        $this->notMandatoryLazy = $notMandatoryLazy;
    }

    public function getNonFileProperty(): \stdClass
    {
        return $this->nonFileProperty;
    }

    public function setNonFileProperty(\stdClass $nonFileProperty): void
    {
        $this->nonFileProperty = $nonFileProperty;
    }

    public function getFileWithoutAttribute(): FileInterface
    {
        return $this->fileWithoutAttribute;
    }

    public function setFileWithoutAttribute(FileInterface $fileWithoutAttribute): void
    {
        $this->fileWithoutAttribute = $fileWithoutAttribute;
    }
}
