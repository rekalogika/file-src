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

use Psr\Http\Message\StreamInterface;
use Rekalogika\Contracts\File\DirectoryInterface;
use Rekalogika\Contracts\File\NodeInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use ZipStream\ZipStream;

final readonly class FileZip
{
    public function __construct(
        private ZipDirectory $zipDirectory,
        private TranslatorInterface $translator,
    ) {}

    /**
     * @param DirectoryInterface<array-key,NodeInterface> $directory
     * @param resource|StreamInterface|null $outputStream
     */
    public function streamZip(
        DirectoryInterface $directory,
        $outputStream = null,
        bool $sendHttpHeaders = true,
    ): void {
        $name = $directory->getName();
        $name->setExtension('zip');

        $zip = new ZipStream(
            outputName: $name->trans($this->translator),
            contentType: 'application/zip',
            outputStream: $outputStream,
            sendHttpHeaders: $sendHttpHeaders,
        );

        $this->zipDirectory->with(
            zip: $zip,
            directory: $directory,
            directoryName: '',
            parent: null,
        )->process();

        $zip->finish();
    }

    /**
     * @param DirectoryInterface<array-key,NodeInterface> $directory
     */
    public function createZipResponse(DirectoryInterface $directory): Response
    {
        return new StreamedResponse(function () use ($directory): void {
            $this->streamZip($directory);
        });
    }
}
