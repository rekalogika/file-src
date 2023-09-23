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

namespace Rekalogika\File\Type;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class TranslatableTypeDescription implements TranslatableInterface, \Stringable
{
    public function __construct(
        private string $description,
    ) {
    }

    public function __toString(): string
    {
        return $this->description;
    }

    public function trans(
        TranslatorInterface $translator,
        ?string $locale = null
    ): string {
        return $translator->trans(
            $this->description,
            [],
            'rekalogika-file-type',
            $locale
        );
    }
}
