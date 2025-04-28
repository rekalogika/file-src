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

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final readonly class AsFileAssociation
{
    /**
     * @var 'EAGER'|'LAZY'
     */
    public string $fetch;

    /**
     * @param 'EAGER'|'LAZY' $fetch
     */
    public function __construct(
        string $fetch = 'EAGER',
    ) {
        if (!\in_array($fetch, ['EAGER', 'LAZY'])) {
            throw new \InvalidArgumentException('Fetch mode can only be EAGER or LAZY.');
        }

        $this->fetch = $fetch;
    }
}
