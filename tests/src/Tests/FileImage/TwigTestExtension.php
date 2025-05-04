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

namespace Rekalogika\File\Tests\Tests\FileImage;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class TwigTestExtension extends AbstractExtension
{
    /**
     * @param array<string,mixed> $variables
     */
    public function __construct(
        private array $variables,
    ) {}

    #[\Override]
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'getvar',
                function (string $name): mixed {
                    return $this->variables[$name] ?? throw new \InvalidArgumentException(
                        \sprintf('Variable "%s" not found', $name),
                    );
                },
            ),
        ];
    }
}
