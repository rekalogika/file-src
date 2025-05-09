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
     * @var \WeakMap<object,\ArrayObject<string,mixed>>
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
        /** @var \WeakMap<object,\ArrayObject<string,mixed>> */
        $properties = new \WeakMap();

        $this->properties = $properties;
    }

    public function saveInitialProperty(
        object $object,
        string $propertyName,
        mixed $value,
    ): void {
        if (!isset($this->properties[$object])) {
            /** @var \ArrayObject<string,mixed> */
            $arrayObject = new \ArrayObject();

            $this->properties->offsetSet($object, $arrayObject);
        }

        $this->properties
            ->offsetGet($object)
            ->offsetSet($propertyName, $value);
    }

    public function getInitialProperty(
        object $object,
        string $propertyName,
    ): mixed {
        if (!isset($this->properties[$object])) {
            return null;
        }

        return $this->properties
            ->offsetGet($object)
            ->offsetGet($propertyName);
    }

    public function hasInitialProperty(
        object $object,
        string $propertyName,
    ): bool {
        if (!isset($this->properties[$object])) {
            return false;
        }

        return $this->properties
            ->offsetGet($object)
            ->offsetExists($propertyName);
    }

    public function removeInitialProperty(
        object $object,
        string $propertyName,
    ): void {
        if (!isset($this->properties[$object])) {
            return;
        }

        $this->properties
            ->offsetGet($object)
            ->offsetUnset($propertyName);
    }
}
