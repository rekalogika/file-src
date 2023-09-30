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

namespace Rekalogika\Contracts\File\Trait;

use Psr\Http\Message\StreamInterface;
use Rekalogika\Contracts\File\FileInterface;
use Rekalogika\Contracts\File\FilePointerInterface;

trait FileDecoratorTrait
{
    use EqualityTrait;
    use MetadataTrait;

    abstract private function getWrapped(): FileInterface;

    public function getPointer(): FilePointerInterface
    {
        return $this->getWrapped()->getPointer();
    }

    public function setContent(string $contents): void
    {
        $this->getWrapped()->setContent($contents);
    }

    /**
     * @param resource|StreamInterface $stream
     */
    public function setContentFromStream(mixed $stream): void
    {
        $this->getWrapped()->setContentFromStream($stream);
    }

    public function getContent(): string
    {
        return $this->getWrapped()->getContent();
    }

    public function getContentAsStream(): StreamInterface
    {
        return $this->getWrapped()->getContentAsStream();
    }

    public function saveToLocalFile(string $path): \SplFileInfo
    {
        return $this->getWrapped()->saveToLocalFile($path);
    }

    public function createLocalTemporaryFile(): \SplFileInfo
    {
        return $this->getWrapped()->createLocalTemporaryFile();
    }

    public function getDerivation(string $derivationId): FilePointerInterface
    {
        return $this->getWrapped()->getDerivation($derivationId);
    }

    public function getFilesystemIdentifier(): ?string
    {
        return $this->getWrapped()->getFilesystemIdentifier();
    }

    public function getKey(): string
    {
        return $this->getWrapped()->getKey();
    }

    public function get(string $id): mixed
    {
        return $this->getWrapped()->get($id);
    }

    public function flush(): void
    {
        $this->getWrapped()->flush();
    }
}
