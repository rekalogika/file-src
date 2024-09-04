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

namespace Rekalogika\File\Bridge\Symfony\HttpFoundation;

use Rekalogika\Contracts\File\FileInterface;
use Rekalogika\File\File;
use Symfony\Component\HttpFoundation\File\File as HttpFoundationFile;

/**
 * Adapter to convert a FileInterface into a HttpFoundation File object. This
 * works with a remote file by copying the remote file to a local temporary file
 * and then passing that to the HttpFoundation File constructor. If the file is
 * local, it will be used directly.
 */
class ToHttpFoundationFileAdapter extends HttpFoundationFile
{
    /**
     * We keep the original output of createLocalTemporaryFile here to prevent
     * it from being __destructed
     */
    private ?\SplFileInfo $localTemporaryFile = null;

    /**
     * The output of createLocalTemporaryFile() is converted to
     * HttpFoundationFile and stored here
     */
    private ?HttpFoundationFile $httpFoundationFile = null;

    private function __construct(private readonly FileInterface $file) {}

    public static function adapt(FileInterface $file): HttpFoundationFile
    {
        // prevent adaptception
        if ($file instanceof FromHttpFoundationFileAdapter) {
            return $file->getWrapped();
        }

        // if file is a local file, use that directly without a temporary file
        if ($file instanceof File && $file->isLocalFilesystem()) {
            return new HttpFoundationFile($file->getKey());
        }

        return new self($file);
    }

    private function getHttpFoundationFile(): HttpFoundationFile
    {
        if ($this->httpFoundationFile !== null) {
            return $this->httpFoundationFile;
        }

        $this->localTemporaryFile = $this->file->createLocalTemporaryFile();
        $this->httpFoundationFile = new HttpFoundationFile(
            $this->localTemporaryFile->getPathname(),
        );

        return $this->httpFoundationFile;
    }

    public function getWrapped(): FileInterface
    {
        return $this->file;
    }

    //
    // File methods
    //

    #[\Override]
    public function guessExtension(): ?string
    {
        return $this->getHttpFoundationFile()->guessExtension();
    }

    #[\Override]
    public function getMimeType(): ?string
    {
        return $this->getHttpFoundationFile()->getMimeType();
    }

    #[\Override]
    public function move(string $directory, string $name = null): self
    {
        $this->getHttpFoundationFile()->move($directory, $name);

        return $this;
    }

    #[\Override]
    public function getContent(): string
    {
        return $this->getHttpFoundationFile()->getContent();
    }

    //
    // \SplFileInfo methods
    //

    #[\Override]
    public function getATime(): int|false
    {
        return $this->getHttpFoundationFile()->getATime();
    }

    #[\Override]
    public function getBasename(string $suffix = ''): string
    {
        return $this->getHttpFoundationFile()->getBasename($suffix);
    }

    #[\Override]
    public function getCTime(): int|false
    {
        return $this->getHttpFoundationFile()->getCTime();
    }

    #[\Override]
    public function getExtension(): string
    {
        return $this->getHttpFoundationFile()->getExtension();
    }

    /**
     * @param class-string|null $class
     */
    #[\Override]
    public function getFileInfo($class = null): \SplFileInfo
    {
        return $this->getHttpFoundationFile()->getFileInfo($class);
    }

    #[\Override]
    public function getFilename(): string
    {
        return $this->getHttpFoundationFile()->getFilename();
    }

    #[\Override]
    public function getGroup(): int|false
    {
        return $this->getHttpFoundationFile()->getGroup();
    }

    #[\Override]
    public function getInode(): int|false
    {
        return $this->getHttpFoundationFile()->getInode();
    }

    #[\Override]
    public function getLinkTarget(): string|false
    {
        return $this->getHttpFoundationFile()->getLinkTarget();
    }

    #[\Override]
    public function getMTime(): int|false
    {
        return $this->getHttpFoundationFile()->getMTime();
    }

    #[\Override]
    public function getOwner(): int|false
    {
        return $this->getHttpFoundationFile()->getOwner();
    }

    #[\Override]
    public function getPath(): string
    {
        return $this->getHttpFoundationFile()->getPath();
    }

    /**
     * @param class-string|null $class
     */
    #[\Override]
    public function getPathInfo($class = null): ?\SplFileInfo
    {
        return $this->getHttpFoundationFile()->getPathInfo($class);
    }

    #[\Override]
    public function getPathname(): string
    {
        return $this->getHttpFoundationFile()->getPathname();
    }

    #[\Override]
    public function getPerms(): int|false
    {
        return $this->getHttpFoundationFile()->getPerms();
    }

    /** @psalm-suppress LessSpecificImplementedReturnType */
    #[\Override]
    public function getRealPath(): string|false
    {
        return $this->getHttpFoundationFile()->getRealPath();
    }

    #[\Override]
    public function getSize(): int|false
    {
        return $this->getHttpFoundationFile()->getSize();
    }

    #[\Override]
    public function getType(): string|false
    {
        return $this->getHttpFoundationFile()->getType();
    }

    #[\Override]
    public function isDir(): bool
    {
        return $this->getHttpFoundationFile()->isDir();
    }

    #[\Override]
    public function isExecutable(): bool
    {
        return $this->getHttpFoundationFile()->isExecutable();
    }

    #[\Override]
    public function isFile(): bool
    {
        return $this->getHttpFoundationFile()->isFile();
    }

    #[\Override]
    public function isLink(): bool
    {
        return $this->getHttpFoundationFile()->isLink();
    }

    #[\Override]
    public function isReadable(): bool
    {
        return $this->getHttpFoundationFile()->isReadable();
    }

    #[\Override]
    public function isWritable(): bool
    {
        return $this->getHttpFoundationFile()->isWritable();
    }

    /**
     * @param ?resource $context
     */
    #[\Override]
    public function openFile(
        string $mode = 'r',
        bool $useIncludePath = false,
        $context = null,
    ): \SplFileObject {
        return $this->getHttpFoundationFile()
            ->openFile($mode, $useIncludePath, $context);
    }

    /**
     * @param class-string $class
     */
    #[\Override]
    public function setFileClass($class = \SplFileObject::class): void
    {
        $this->getHttpFoundationFile()->setFileClass($class);
    }

    /**
     * @param class-string $class
     */
    #[\Override]
    public function setInfoClass($class = \SplFileInfo::class): void
    {
        $this->getHttpFoundationFile()->setInfoClass($class);
    }
}
