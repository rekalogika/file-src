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

/**
 * The result of a property inspection.
 */
final class PropertyInspectorResult
{
    /**
     * @param 'EAGER'|'LAZY' $fetch
     */
    public function __construct(
        private readonly bool $mandatory,
        private readonly string $fetch,
    ) {
        if (!in_array($fetch, ['EAGER', 'LAZY'])) {
            throw new \InvalidArgumentException('Fetch mode can only be EAGER or LAZY.');
        }
    }

    public function isMandatory(): bool
    {
        return $this->mandatory;
    }

    public function getFetch(): string
    {
        return $this->fetch;
    }
}
