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

namespace Rekalogika\Contracts\File;

use Symfony\Contracts\Translation\TranslatableInterface;

/**
 * Represents a MIME content-type
 */
interface FileTypeInterface extends \Stringable
{
    /**
     * The MIME type identifier for the type, like 'image/jpeg'.
     */
    public function getName(): string;

    /**
     * The MIME media type for the type, like 'image'.
     */
    public function getType(): string;

    /**
     * The MIME subtype for the type, like 'jpeg'.
     */
    public function getSubType(): string;

    /**
     * Gets the list of common file extensions for the type, in lowercase,
     * without the leading dot.
     *
     * @return array<array-key,string>
     */
    public function getCommonExtensions(): array;

    /**
     * Gets the best file extension for the type, in lowercase, without the
     * leading dot.
     */
    public function getExtension(): ?string;

    /**
     * Description of the type
     */
    public function getDescription(): string|(\Stringable&TranslatableInterface);
}
