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

namespace Rekalogika\File\Association\Attribute;

use Rekalogika\File\Association\Exception\InvalidClassSignatureException;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class WithFileAssociation
{
    public function __construct(
        private readonly ?string $signature = null,
    ) {
        // if not null, ensure $classSignature contains only alphanumeric characters only

        if (
            $signature !== null
            && !preg_match('/^[a-zA-Z0-9_]+$/', $signature)
        ) {
            throw new InvalidClassSignatureException(\sprintf(
                'Class signature must contain only alphanumeric characters and underscores, "%s" given',
                $signature,
            ));
        }
    }

    public function getSignature(): ?string
    {
        return $this->signature;
    }
}
