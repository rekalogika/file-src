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

namespace Rekalogika\Domain\File\Null;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class TranslatableMessage implements \Stringable, TranslatableInterface
{
    public function __construct(
        private readonly string $name,
        private readonly string $translationDomain
    ) {
    }

    #[\Override]
    public function __toString(): string
    {
        return $this->name;
    }

    #[\Override]
    public function trans(
        TranslatorInterface $translator,
        ?string $locale = null
    ): string {
        return $translator->trans(
            $this->name,
            [],
            $this->translationDomain,
            $locale
        );
    }
}
