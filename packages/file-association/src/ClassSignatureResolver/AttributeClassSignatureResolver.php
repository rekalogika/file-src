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

namespace Rekalogika\File\Association\ClassSignatureResolver;

use Rekalogika\File\Association\Attribute\WithFileAssociation;
use Rekalogika\File\Association\Contracts\ClassSignatureResolverInterface;

final readonly class AttributeClassSignatureResolver implements ClassSignatureResolverInterface
{
    #[\Override]
    public function getClassSignature(string $class): ?string
    {
        $reflectionClass = new \ReflectionClass($class);

        $attribute = $reflectionClass
            ->getAttributes(WithFileAssociation::class)[0] ?? null;

        if ($attribute === null) {
            return null;
        }

        $attributeInstance = $attribute->newInstance();

        return $attributeInstance->getSignature();
    }
}
