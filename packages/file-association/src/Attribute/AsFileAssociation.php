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
    public function __construct(
        public FetchMode $fetch = FetchMode::Eager,
    ) {}
}
