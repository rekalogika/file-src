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

namespace Rekalogika\Domain\File\Metadata\Model;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Translatable string for file name
 */
final class TranslatableMessage implements TranslatableInterface, \Stringable
{
    /**
     * @param array<string,string> $parameters
     */
    public function __construct(
        private string $stringName,
        private string $translationId,
        private array $parameters = []
    ) {
    }

    public function __toString(): string
    {
        return $this->stringName;
    }

    public function trans(
        TranslatorInterface $translator,
        ?string $locale = null
    ): string {
        return $translator->trans(
            $this->translationId,
            $this->parameters,
            'rekalogika_file',
            $locale
        );
    }
}
