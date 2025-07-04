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

namespace Rekalogika\File\Tests\Tests\Model;

final readonly class EntityWithAnyId
{
    public function __construct(
        private mixed $id,
    ) {}

    public function getId(): mixed
    {
        return $this->id;
    }
}
