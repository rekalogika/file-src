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

class EntityWithAnyId
{
    public function __construct(
        private readonly mixed $id,
    ) {}

    public function getId(): mixed
    {
        return $this->id;
    }
}
