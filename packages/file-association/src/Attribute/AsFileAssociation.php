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

use Rekalogika\File\Association\Model\FetchMode;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final readonly class AsFileAssociation
{
    public FetchMode $fetch;

    /**
     * @param 'EAGER'|'LAZY'|FetchMode $fetch
     */
    public function __construct(
        FetchMode|string $fetch = FetchMode::Eager,
    ) {
        if (\is_string($fetch)) {
            trigger_deprecation(
                package: 'rekalogika/file-association',
                version: '2.0.0',
                message: 'Passing a string as the first argument to "%s" is deprecated, use the FetchMode enum instead.',
                args: __CLASS__,
            );

            $fetch = match (strtoupper($fetch)) {
                'EAGER' => FetchMode::Eager,
                // @phpstan-ignore match.alwaysTrue
                'LAZY' => FetchMode::Lazy,
                default => throw new \InvalidArgumentException(\sprintf('Invalid fetch mode "%s".', $fetch)),
            };
        }

        $this->fetch = $fetch;
    }
}
