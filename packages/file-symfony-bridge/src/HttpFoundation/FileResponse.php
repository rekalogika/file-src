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
use Rekalogika\Contracts\File\Metadata\HttpMetadataInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileResponse extends StreamedResponse
{
    /**
     * @param int $status HTTP status code (200 OK by default)
     * @param array<string,string> $headers HTTP headers
     */
    public function __construct(
        FileInterface $file,
        int $status = 200,
        array $headers = [],
        ?string $disposition = null,
    ) {
        $headersFromMetadata = $file->get(HttpMetadataInterface::class)
            ?->getHeaders($disposition) ?? [];

        if ($headersFromMetadata instanceof \Traversable) {
            $headersFromMetadata = \iterator_to_array($headersFromMetadata);
        }

        $responseHeaders = [
            ... array_change_key_case($headersFromMetadata, \CASE_LOWER),
            ... array_change_key_case($headers, \CASE_LOWER),
        ];

        $inputStream = $file->getContentAsStream()->detach();
        assert($inputStream !== null);

        $callback = function () use ($inputStream): void {
            $outputStream = fopen('php://output', 'wb');
            if ($outputStream === false) {
                throw new \RuntimeException('Failed to open output stream');
            }

            $result = stream_copy_to_stream($inputStream, $outputStream);
            if ($result === false) {
                throw new \RuntimeException('Failed to copy stream');
            }
        };

        parent::__construct($callback, $status, $responseHeaders);
    }
}
