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

namespace Rekalogika\File\Association\Contracts;

interface FilePropertyManagerInterface
{
    /**
     * Process a potential incoming file upload on a property
     *
     * @param class-string $class
     */
    public function saveProperty(
        object $object,
        string $class,
        string $id,
        string $propertyName,
    ): void;

    /**
     * Process a file removal on a property
     *
     * @param class-string $class
     */
    public function removeProperty(
        object $object,
        string $class,
        string $id,
        string $propertyName,
    ): void;

    /**
     * Process a property on an object load
     *
     * @param class-string $class
     */
    public function loadProperty(
        object $object,
        string $class,
        string $id,
        string $propertyName,
    ): void;
}
