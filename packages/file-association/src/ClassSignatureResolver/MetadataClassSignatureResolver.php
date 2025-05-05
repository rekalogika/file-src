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

use Rekalogika\File\Association\Contracts\ClassMetadataFactoryInterface;
use Rekalogika\File\Association\Contracts\ClassSignatureResolverInterface;

/**
 * Gets the class signature already saved in the metadata
 */
final readonly class MetadataClassSignatureResolver implements ClassSignatureResolverInterface
{
    public function __construct(
        private ClassMetadataFactoryInterface $classMetadataFactory,
    ) {}

    #[\Override]
    public function getClassSignature(string $class): string
    {
        return $this->classMetadataFactory
            ->getClassMetadata($class)
            ->getSignature();
    }
}
