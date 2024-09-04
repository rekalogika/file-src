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

use Rekalogika\Contracts\File\RawMetadataInterface;

/**
 * Examines a local file, string, or stream, and generate metadata from it.
 */
interface MetadataGeneratorInterface
{
    /**
     * Generates metadata from a local file
     */
    public function generateMetadataFromFile(
        RawMetadataInterface $rawMetadata,
        string|\SplFileInfo $file,
    ): void;

    /**
     * Generates metadata from a string
     */
    public function generateMetadataFromString(
        RawMetadataInterface $rawMetadata,
        string $content,
    ): void;

    /**
     * Generates metadata from a stream
     *
     * @param resource $stream
     */
    public function generateMetadataFromStream(
        RawMetadataInterface $rawMetadata,
        mixed $stream,
        int $length = 32768,
    ): void;
}
