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

namespace Rekalogika\Contracts\File\Metadata;

/**
 * Represent HTTP metadata of a file.
 */
interface HttpMetadataInterface
{
    /**
     * Returns all HTTP headers.
     *
     * @return iterable<string,string>
     */
    public function getHeaders(?string $disposition = null): iterable;

    /**
     * Gets the Cache-Control header
     */
    public function getCacheControl(): ?string;

    /**
     * Sets the Cache-Control header
     */
    public function setCacheControl(?string $cacheControl): void;

    /**
     * Gets the disposition. Will be used in Content-Disposition header.
     */
    public function getDisposition(): string;

    /**
     * Sets the disposition. Will be used in Content-Disposition header.
     */
    public function setDisposition(string $disposition): void;
}
