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
 * Represents a file name
 */
interface FileNameInterface extends \Stringable, TranslatableInterface
{
    /**
     * The full filename, with extension.
     */
    public function getFull(): \Stringable&TranslatableInterface;

    /**
     * Set the full filename, with extension.
     */
    public function setFull(string $name): void;

    /**
     * The base of the filename, without extension.
     */
    public function getBase(): \Stringable&TranslatableInterface;

    /**
     * Set the base of the filename, without extension.
     */
    public function setBase(string $name): void;

    /**
     * The extension of the filename, without the dot, in lower case
     */
    public function getExtension(): ?string;

    /**
     * Set the extension of the filename, without the dot.
     */
    public function setExtension(?string $extension): void;

    /**
     * Whether the filename has an extension.
     */
    public function hasExtension(): bool;
}
