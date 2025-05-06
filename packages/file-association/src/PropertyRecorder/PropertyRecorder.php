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

namespace Rekalogika\File\Association\PropertyRecorder;

use Symfony\Contracts\Service\ResetInterface;

final class PropertyRecorder implements ResetInterface
{
    /**
     * @var \WeakMap<object,array<string,mixed>>
     */
    private \WeakMap $properties;

    public function __construct()
    {
        $this->init();
    }

    #[\Override]
    public function reset(): void
    {
        $this->init();
    }

    private function init(): void
    {
        /** @var \WeakMap<object,array<string,mixed>> */
        $properties = new \WeakMap();
        $this->properties = $properties;
    }

    public function saveInitialProperty(
        object $object,
        string $propertyName,
        mixed $value,
    ): void {
        if (!isset($this->properties[$object])) {
            $this->properties[$object] = [];
        }

        /** @psalm-suppress MixedArgument */
        $this->properties[$object][$propertyName] = $value;
    }

    public function getInitialProperty(
        object $object,
        string $propertyName,
    ): mixed {
        if (!isset($this->properties[$object])) {
            return null;
        }

        /** @psalm-suppress PossiblyNullArrayAccess */
        return $this->properties[$object][$propertyName] ?? null;
    }

    public function hasInitialProperty(
        object $object,
        string $propertyName,
    ): bool {
        if (!isset($this->properties[$object])) {
            return false;
        }

        /** @psalm-suppress PossiblyNullArrayAccess */
        return isset($this->properties[$object][$propertyName]);
    }

    public function removeInitialProperty(
        object $object,
        string $propertyName,
    ): void {
        if (!isset($this->properties[$object])) {
            return;
        }

        /** @psalm-suppress PossiblyNullArrayAccess */
        unset($this->properties[$object][$propertyName]);
    }
}
