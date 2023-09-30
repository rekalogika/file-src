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

use Rekalogika\File\Association\Exception\PropertyInspector\PropertyInspectorException;
use Rekalogika\File\Association\Model\PropertyInspectorResult;

/**
 * Writes a valuo to a property of an object.
 *
 * @throws PropertyInspectorException
 */
interface PropertyInspectorInterface
{
    public function inspect(
        object $object,
        string $propertyName,
    ): PropertyInspectorResult;
}
