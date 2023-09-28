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

namespace Rekalogika\File\MetadataGenerator;

use League\MimeTypeDetection\FinfoMimeTypeDetector;
use League\MimeTypeDetection\MimeTypeDetector;
use Rekalogika\Contracts\File\Exception\File\LocalTemporaryFileException;
use Rekalogika\Contracts\File\RawMetadataInterface;
use Rekalogika\File\LocalTemporaryFile;
use Rekalogika\Domain\File\Metadata\Constants;

final class MetadataGenerator implements MetadataGeneratorInterface
{
    private MimeTypeDetector $mimeTypeDetector;

    public function __construct(
        ?MimeTypeDetector $mimeTypeDetector = null
    ) {
        $this->mimeTypeDetector = $mimeTypeDetector ?? new FinfoMimeTypeDetector();
    }

    public function generateMetadataFromFile(
        RawMetadataInterface $rawMetadata,
        string|\SplFileInfo $file,
        bool $includeContentLength = true,
    ): void {
        $path = $file instanceof \SplFileInfo
            ? $file->getPathname()
            : $file;

        $rawMetadata->set(
            Constants::FILE_TYPE,
            $this->mimeTypeDetector
                ->detectMimeTypeFromFile($path)
                ?? 'application/octet-stream'
        );

        $imagesize = $this->getImageSize($path);

        if ($imagesize[0] !== null) {
            $rawMetadata->set(Constants::MEDIA_WIDTH, $imagesize[0]);
        } else {
            $rawMetadata->delete(Constants::MEDIA_WIDTH);
        }

        if ($imagesize[1] !== null) {
            $rawMetadata->set(Constants::MEDIA_HEIGHT, $imagesize[1]);
        } else {
            $rawMetadata->delete(Constants::MEDIA_HEIGHT);
        }

        if ($includeContentLength) {
            $filesize = filesize($path);

            if ($filesize !== false) {
                $rawMetadata->set(Constants::FILE_SIZE, $filesize);
            }
        }
    }

    public function generateMetadataFromString(
        RawMetadataInterface $rawMetadata,
        string $content
    ): void {
        $tempFile = $this->createTemporaryFileFromString($content);

        $this->generateMetadataFromFile($rawMetadata, $tempFile);
    }

    public function generateMetadataFromStream(
        RawMetadataInterface $rawMetadata,
        mixed $stream,
        int $length = 32768,
    ): void {
        $tempFile = $this->createTemporaryFileFromStream($stream, $length);

        $this->generateMetadataFromFile($rawMetadata, $tempFile, false);
    }

    private function createTemporaryFileFromString(
        string $contents,
    ): LocalTemporaryFile {
        $tempFile = LocalTemporaryFile::create('metadata-detector-');

        file_put_contents($tempFile->getPathname(), $contents);

        return $tempFile;
    }

    /**
     * @param resource $stream
     */
    private function createTemporaryFileFromStream(
        mixed $stream,
        int $length = 32768,
    ): LocalTemporaryFile {
        $tempFile = LocalTemporaryFile::create('metadata-detector-');

        $output = fopen($tempFile->getPathname(), 'wb');

        if ($output === false) {
            throw new LocalTemporaryFileException('Failed to create local temporary file for gathering metadata.');
        }

        stream_copy_to_stream($stream, $output, $length);
        fclose($output);

        return $tempFile;
    }

    /**
     * @return array{0: int|null, 1: int|null}
     */
    private function getImageSize(string $path): array
    {
        $result = @getimagesize($path);

        if ($result === false) {
            return [null, null];
        }

        return [
            $result[0],
            $result[1],
        ];
    }
}
