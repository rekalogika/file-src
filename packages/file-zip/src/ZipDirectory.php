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

use Rekalogika\Contracts\File\FileInterface;
use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use ZipStream\ZipStream;

/**
 * @internal
 */
final class ZipDirectory
{
    private ZipStream $zip;
    private string $prefix = '';

    /**
     * @var array<array-key,int>
     */
    private array $filename = [];

    /**
     * @var iterable<mixed,mixed> $files
     */
    private iterable $files = [];

    public function __construct(
        private ?TranslatorInterface $translator = null,
    ) {
    }

    /**
     * @param iterable<mixed,mixed> $files
     */
    public function with(
        ZipStream $zip,
        string $directoryName,
        iterable $files,
    ): static {
        if ($directoryName && !ctype_alpha($directoryName)) {
            throw new \InvalidArgumentException(
                'Directory name must be alpha characters only.'
            );
        }

        $clone = clone $this;

        if ($directoryName != '') {
            $clone->prefix = $directoryName . '/';
        } else {
            $clone->prefix = '';
        }

        $clone->zip = $zip;
        $clone->files = $files;
        $clone->filename = [];

        return $clone;
    }

    public function process(): void
    {
        foreach ($this->files as $key => $member) {
            if ($member instanceof FileInterface) {
                $this->processFile($member);
            } elseif (\is_iterable($member)) {
                if (!is_string($key)) {
                    continue;
                }

                $this->with(
                    zip: $this->zip,
                    directoryName: $key,
                    files: $member,
                )->process();
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

    private function getFileName(FileInterface $file): string
    {
        $filename = $this->translate($file->getName()->getBase());
        $extension = $file->getName()->getExtension();

        if ($extension) {
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

        return $this->prefix . $filename . $extension;
    }

    private function translate(TranslatableInterface&\Stringable $message): string
    {
        if ($this->translator) {
            return $message->trans($this->translator, $this->translator->getLocale());
        } else {
            return (string) $message;
        }
    }
}
