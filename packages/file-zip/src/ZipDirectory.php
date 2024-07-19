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

namespace Rekalogika\File\Zip;

use Rekalogika\Contracts\File\DirectoryInterface;
use Rekalogika\Contracts\File\FileInterface;
use Rekalogika\Contracts\File\FilePointerInterface;
use Rekalogika\Contracts\File\FileRepositoryInterface;
use Rekalogika\Contracts\File\NodeInterface;
use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use ZipStream\ZipStream;

/**
 * @internal
 */
final class ZipDirectory
{
    /**
     * @var array<array-key,int>
     */
    private array $filename = [];

    private ZipStream $zip;

    /**
     * @var DirectoryInterface<array-key,NodeInterface>
     */
    private DirectoryInterface $directory;

    private string $directoryName = '';

    private ?self $parent = null;

    private ?string $directoryPathCache = null;

    public function __construct(
        private FileRepositoryInterface $fileRepository,
        private ?TranslatorInterface $translator = null,
    ) {
    }

    /**
     * @param DirectoryInterface<array-key,NodeInterface> $directory
     */
    public function with(
        ZipStream $zip,
        DirectoryInterface $directory,
        string $directoryName,
        ?self $parent,
    ): static {
        $clone = clone $this;
        $clone->zip = $zip;
        $clone->directory = $directory;
        $clone->filename = [];
        $clone->directoryName = $directoryName;
        $clone->parent = $parent;
        $clone->directoryPathCache = null;

        return $clone;
    }

    public function process(): void
    {
        foreach ($this->directory as $node) {
            if ($node instanceof FilePointerInterface) {
                $file = $this->fileRepository->tryGet($node);
                if ($file === null) {
                    continue;
                }

                $this->processFile($file);
            } elseif ($node instanceof FileInterface) {
                $this->processFile($node);
            } elseif ($node instanceof DirectoryInterface) {
                $this->processDirectory($node);
            }
        }
    }

    private function processFile(FileInterface $file): void
    {
        $this->zip->addFileFromPsr7Stream(
            fileName: $this->getFileName($file),
            stream: $file->getContentAsStream(),
            lastModificationDateTime: $file->getLastModified(),
        );
    }

    /**
     * @param DirectoryInterface<array-key,NodeInterface> $directory
     */
    private function processDirectory(DirectoryInterface $directory): void
    {
        $this->with(
            zip: $this->zip,
            directory: $directory,
            directoryName: $this->getFileName($directory),
            parent: $this,
        )->process();
    }

    /**
     * @param DirectoryInterface<array-key,NodeInterface>|FileInterface $file
     */
    private function getFileName(FileInterface|DirectoryInterface $file): string
    {
        $filename = $this->translate($file->getName()->getBase());
        $extension = $file->getName()->getExtension();

        if ($extension !== null && $extension !== '') {
            $extension = '.' . $extension;
        } else {
            $extension = '';
        }

        if (isset($this->filename[$filename])) {
            $this->filename[$filename]++;
            $filename = $filename . ' (' . $this->filename[$filename] . ')';
        } else {
            $this->filename[$filename] = 0;
        }

        return $this->getDirectoryPath() . $filename . $extension;
    }

    public function getDirectoryPath(): string
    {
        if ($this->directoryPathCache !== null) {
            return $this->directoryPathCache;
        }

        if ($this->parent !== null) {
            $result = sprintf(
                '%s/%s/',
                $this->parent->getDirectoryPath(),
                $this->directoryName
            );
        } else {
            $result = $this->directoryName . '/';
        }

        // trim slash at the beginning
        if (str_starts_with($result, '/')) {
            $result = substr($result, 1);
        }

        return $this->directoryPathCache = $result;
    }

    private function translate(TranslatableInterface&\Stringable $message): string
    {
        if ($this->translator !== null) {
            return $message->trans($this->translator, $this->translator->getLocale());
        }

        return (string) $message;

    }
}
