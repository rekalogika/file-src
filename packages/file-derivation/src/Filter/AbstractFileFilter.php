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

namespace Rekalogika\File\Derivation\Filter;

use Rekalogika\Contracts\File\Exception\File\FileNotFoundException;
use Rekalogika\Contracts\File\FileInterface;
use Rekalogika\Contracts\File\FilePointerInterface;
use Rekalogika\Contracts\File\FileRepositoryInterface;

abstract class AbstractFileFilter implements FileFilterInterface
{
    private FileRepositoryInterface $fileRepository;

    private FileInterface $sourceFile;

    /**
     * Will be executed by the container to inject the file repository
     */
    #[\Override]
    final public function setFileRepository(
        FileRepositoryInterface $fileRepository
    ): void {
        $this->fileRepository = $fileRepository;
    }

    final public function getFileRepository(): FileRepositoryInterface
    {
        return $this->fileRepository;
    }

    /**
     * Caller must call this method to set the source file
     */
    final public function take(
        FileInterface $sourceFile
    ): static {
        $clone = clone $this;
        $clone->sourceFile = $sourceFile;

        return $clone;
    }

    //
    // methods available to subclasses
    //

    protected function getSourceFile(): FileInterface
    {
        return $this->sourceFile;
    }

    protected function getDerivationFilePointer(): FilePointerInterface
    {
        return $this->sourceFile->getDerivation($this->getDerivationId());
    }

    //
    // methods to be implemented by subclasses
    //

    /**
     * Determines the derivation ID according to the parameters given by the
     * caller
     */
    abstract protected function getDerivationId(): string;

    /**
     * Takes the original file from $this->sourceFile, create a derivation from
     * it, then return the derivation file. This method may write directly to
     * $this->getDestinationFilePointer() and return the result. Or it can
     * return a temporary file, in which case, this class will copy it to the
     * destination pointer.
     */
    abstract protected function process(): FileInterface;

    //
    // public methods called by the caller
    //

    /**
     * Caller calls this to get the result
     */
    public function getResult(): FileInterface
    {
        if (!isset($this->sourceFile)) {
            throw new \LogicException(
                'Call "take()" first before calling "getResult()"'
            );
        }

        $derivationFilePointer = $this->getDerivationFilePointer();

        try {
            $derivationFile = $this->fileRepository->get($derivationFilePointer);
        } catch (FileNotFoundException) {
            $derivationFile = null;
        }

        if (
            $derivationFile !== null
            && $derivationFile->getLastModified() >= $this->sourceFile->getLastModified()
        ) {
            return $derivationFile;
        }

        $result = $this->process();

        if ($result->isEqualTo($derivationFilePointer)) {
            return $result;
        }

        return $this->fileRepository->copy($result, $derivationFilePointer);

    }
}
