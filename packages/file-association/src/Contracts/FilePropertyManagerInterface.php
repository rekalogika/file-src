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

use Rekalogika\File\Association\Model\PropertyMetadata;
use Rekalogika\File\Association\Model\PropertyOperationResult;

/**
 * @internal
 */
interface FilePropertyManagerInterface
{
    /**
     * Process a potential incoming file upload on a property
     */
    public function flushProperty(
        PropertyMetadata $propertyMetadata,
        object $object,
        string $id,
    ): PropertyOperationResult;

    /**
     * Process a file removal on a property
     */
    public function removeProperty(
        PropertyMetadata $propertyMetadata,
        object $object,
        string $id,
    ): PropertyOperationResult;

    /**
     * Process a property on an object load
     */
    public function loadProperty(
        PropertyMetadata $propertyMetadata,
        object $object,
        string $id,
    ): PropertyOperationResult;
}
