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

use Rekalogika\File\Association\Contracts\ClassSignatureResolverInterface;

final readonly class ChainClassSignatureResolver implements ClassSignatureResolverInterface
{
    /**
     * @param iterable<ClassSignatureResolverInterface> $resolvers
     */
    public function __construct(
        private iterable $resolvers,
    ) {}

    #[\Override]
    public function getClassSignature(string $class): string
    {
        foreach ($this->resolvers as $resolver) {
            $signature = $resolver->getClassSignature($class);

            if ($signature !== null) {
                return $signature;
            }
        }

        throw new \LogicException(
            \sprintf('No class signature resolver found for class "%s"', $class),
        );
    }
}
