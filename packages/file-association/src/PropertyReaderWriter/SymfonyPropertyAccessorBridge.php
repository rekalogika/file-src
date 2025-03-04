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

namespace Rekalogika\File\Association\PropertyReaderWriter;

use Rekalogika\File\Association\Contracts\PropertyReaderInterface;
use Rekalogika\File\Association\Contracts\PropertyWriterInterface;
use Rekalogika\File\Association\Exception\PropertyReader\PropertyReaderException;
use Rekalogika\File\Association\Exception\PropertyWriter\PropertyWriterException;
use Symfony\Component\PropertyAccess\Exception\AccessException;
use Symfony\Component\PropertyAccess\Exception\InvalidArgumentException;
use Symfony\Component\PropertyAccess\Exception\UnexpectedTypeException;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface as SymfonyPropertyAccessorInterface;

final class SymfonyPropertyAccessorBridge implements
    PropertyReaderInterface,
    PropertyWriterInterface
{
    public function __construct(
        private readonly SymfonyPropertyAccessorInterface $propertyAccessor,
    ) {}

    #[\Override]
    public function write(object $object, string $propertyName, mixed $value): void
    {
        try {
            $this->propertyAccessor->setValue($object, $propertyName, $value);
        } catch (InvalidArgumentException | AccessException | UnexpectedTypeException $e) {
            throw new PropertyWriterException($object, $propertyName, $value, $e);
        }
    }

    #[\Override]
    public function read(object $object, string $propertyName): mixed
    {
        try {
            return $this->propertyAccessor->getValue($object, $propertyName);
        } catch (InvalidArgumentException | AccessException | UnexpectedTypeException $e) {
            throw new PropertyReaderException($object, $propertyName, $e);
        }
    }
}
