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
     *
     * @var \SplFileInfo|null
     */
    private ?\SplFileInfo $localTemporaryFile = null;

    /**
     * The output of createLocalTemporaryFile() is converted to
     * HttpFoundationFile and stored here
     */
    private ?HttpFoundationFile $httpFoundationFile = null;

    private function __construct(private FileInterface $file)
    {
    }

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
            $this->localTemporaryFile->getPathname()
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

    public function guessExtension(): ?string
    {
        return $this->getHttpFoundationFile()->guessExtension();
    }

    public function getMimeType(): ?string
    {
        return $this->getHttpFoundationFile()->getMimeType();
    }

    public function move(string $directory, string $name = null): self
    {
        $this->getHttpFoundationFile()->move($directory, $name);

        return $this;
    }

    public function getContent(): string
    {
        return $this->getHttpFoundationFile()->getContent();
    }

    //
    // \SplFileInfo methods
    //

    public function getATime(): int|false
    {
        return $this->getHttpFoundationFile()->getATime();
    }

    public function getBasename(string $suffix = ''): string
    {
        return $this->getHttpFoundationFile()->getBasename($suffix);
    }

    public function getCTime(): int|false
    {
        return $this->getHttpFoundationFile()->getCTime();
    }

    public function getExtension(): string
    {
        return $this->getHttpFoundationFile()->getExtension();
    }

    /**
     * @param class-string|null $class
     */
    public function getFileInfo($class = null): \SplFileInfo
    {
        return $this->getHttpFoundationFile()->getFileInfo($class);
    }

    public function getFilename(): string
    {
        return $this->getHttpFoundationFile()->getFilename();
    }

    public function getGroup(): int|false
    {
        return $this->getHttpFoundationFile()->getGroup();
    }

    public function getInode(): int|false
    {
        return $this->getHttpFoundationFile()->getInode();
    }

    public function getLinkTarget(): string|false
    {
        return $this->getHttpFoundationFile()->getLinkTarget();
    }

    public function getMTime(): int|false
    {
        return $this->getHttpFoundationFile()->getMTime();
    }

    public function getOwner(): int|false
    {
        return $this->getHttpFoundationFile()->getOwner();
    }

    public function getPath(): string
    {
        return $this->getHttpFoundationFile()->getPath();
    }

    /**
     * @param class-string|null $class
     */
    public function getPathInfo($class = null): ?\SplFileInfo
    {
        return $this->getHttpFoundationFile()->getPathInfo($class);
    }

    public function getPathname(): string
    {
        return $this->getHttpFoundationFile()->getPathname();
    }

    public function getPerms(): int|false
    {
        return $this->getHttpFoundationFile()->getPerms();
    }

    public function getRealPath(): string|false
    {
        return $this->getHttpFoundationFile()->getRealPath();
    }

    public function getSize(): int|false
    {
        return $this->getHttpFoundationFile()->getSize();
    }

    public function getType(): string|false
    {
        return $this->getHttpFoundationFile()->getType();
    }

    public function isDir(): bool
    {
        return $this->getHttpFoundationFile()->isDir();
    }

    public function isExecutable(): bool
    {
        return $this->getHttpFoundationFile()->isExecutable();
    }

    public function isFile(): bool
    {
        return $this->getHttpFoundationFile()->isFile();
    }

    public function isLink(): bool
    {
        return $this->getHttpFoundationFile()->isLink();
    }

    public function isReadable(): bool
    {
        return $this->getHttpFoundationFile()->isReadable();
    }

    public function isWritable(): bool
    {
        return $this->getHttpFoundationFile()->isWritable();
    }

    /**
     * @param ?resource $context
     */
    public function openFile(
        string $mode = 'r',
        bool $useIncludePath = false,
        $context = null
    ): \SplFileObject {
        return $this->getHttpFoundationFile()
            ->openFile($mode, $useIncludePath, $context);
    }

    /**
     * @param class-string $class
     */
    public function setFileClass($class = \SplFileObject::class): void
    {
        $this->getHttpFoundationFile()->setFileClass($class);
    }

    /**
     * @param class-string $class
     */
    public function setInfoClass($class = \SplFileInfo::class): void
    {
        $this->getHttpFoundationFile()->setInfoClass($class);
    }
}
