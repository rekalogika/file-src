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
use ZipStream\ZipStream;

final class FileZip
{
    private ZipDirectory $zipDirectory;

    public function __construct(?ZipDirectory $zipDirectory = null)
    {
        if (null === $zipDirectory) {
            $zipDirectory = new ZipDirectory();
        }

        $this->zipDirectory = $zipDirectory;
    }

    /**
     * @param iterable<mixed,mixed> $files
     * @param resource|StreamInterface|null $outputStream
     */
    public function zipFiles(
        string $fileName,
        iterable $files,
        $outputStream = null,
        bool $sendHttpHeaders = true,
    ): void {
        $zip = new ZipStream(
            outputName: $fileName,
            contentType: 'application/zip',
            outputStream: $outputStream,
            sendHttpHeaders: $sendHttpHeaders,
        );

        $this->zipDirectory->with(
            zip: $zip,
            directoryName: '',
            files: $files
        )->process();

        $zip->finish();
    }
}
