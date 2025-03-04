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

namespace Rekalogika\Contracts\File;

use Rekalogika\Contracts\File\Exception\File\FileNotFoundException;
use Rekalogika\Contracts\File\Trait\FileDecoratorTrait;

/**
 * Lazy-loading proxy for a FileInterface.
 */
final class FileProxy implements FileInterface
{
    use FileDecoratorTrait;

    //
    // utilities for getters
    //

    public static function getFile(
        FileInterface|null $file,
    ): FileInterface|null {
        if ($file instanceof self) {
            try {
                $file->load();

                return $file->wrapped;
            } catch (FileNotFoundException) {
                return null;
            }
        }

        return $file;
    }

    //
    // properties
    //

    private bool $isFileMissing = false;

    private ?FileInterface $wrapped = null;

    //
    // magic methods
    //

    public function __construct(
        private FilePointerInterface $filePointer,
        private FileRepositoryInterface $fileRepository,
    ) {}

    //
    // proxy related methods
    //

    private function load(): void
    {
        // if file is already loaded, do nothing
        if ($this->wrapped !== null) {
            return;
        }

        // if we already tried fetching the file, but it was missing,
        // we don't want to try again
        if ($this->isFileMissing) {
            $this->throwNotFound();
        }

        // try to fetch the file. if it fails, mark it as missing
        try {
            $this->wrapped = $this->fileRepository->get($this->filePointer);
        } catch (FileNotFoundException) {
            $this->isFileMissing = true;
            $this->throwNotFound();
        }
    }

    #[\Override]
    protected function getWrapped(): FileInterface
    {
        $this->load();

        // safeguard. this should never happen. also, to satisfy static analyzers
        if ($this->wrapped === null) {
            $this->isFileMissing = true;
            $this->throwNotFound();
        }

        return $this->wrapped;
    }

    private function throwNotFound(): never
    {
        throw new FileNotFoundException(
            $this->filePointer->getKey(),
            $this->filePointer->getFilesystemIdentifier(),
        );
    }

    //
    // public methods
    //

    #[\Override]
    public function getPointer(): FilePointerInterface
    {
        return $this->filePointer;
    }

    #[\Override]
    public function getFilesystemIdentifier(): ?string
    {
        return $this->filePointer->getFilesystemIdentifier();
    }

    #[\Override]
    public function getKey(): string
    {
        return $this->filePointer->getKey();
    }
}
