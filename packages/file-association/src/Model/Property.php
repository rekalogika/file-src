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

namespace Rekalogika\File\Association\Model;

final readonly class Property
{
    private string $signature;

    /**
     * @param class-string $class
     */
    public function __construct(
        private string $class,
        private string $name,
    ) {
        $this->signature = \sprintf('%s::%s', $class, $name);
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return class-string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    public function getSignature(): string
    {
        return $this->signature;
    }
}
