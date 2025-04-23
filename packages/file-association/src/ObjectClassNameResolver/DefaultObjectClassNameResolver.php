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

namespace Rekalogika\File\Association\ObjectClassNameResolver;

use Rekalogika\File\Association\Contracts\ObjectClassNameResolverInterface;

final class DefaultObjectClassNameResolver implements ObjectClassNameResolverInterface
{
    #[\Override]
    public function getObjectClassName(object $object): string
    {
        return $object::class;
    }
}
