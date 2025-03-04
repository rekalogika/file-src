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

namespace Rekalogika\File\Association\ObjectIdResolver;

use Doctrine\ORM\UnitOfWork;
use Doctrine\Persistence\ManagerRegistry;
use Rekalogika\File\Association\Contracts\ObjectIdResolverInterface;
use Rekalogika\File\Association\Exception\ObjectIdResolver\ObjectNotSupportedException;

/**
 * Resolves the unique identifier of an object using Doctrine.
 *
 * @todo this code is untested
 */
final class DoctrineObjectIdResolver implements ObjectIdResolverInterface
{
    public function __construct(
        private readonly ManagerRegistry $managerRegistry,
    ) {}

    #[\Override]
    public function getObjectId(object $object): string
    {
        $objectManager = $this->managerRegistry
            ->getManagerForClass($object::class);

        if (!$objectManager) {
            throw new ObjectNotSupportedException($object);
        }

        if (method_exists($objectManager, 'getUnitOfWork')) {
            $unitOfWork = $objectManager->getUnitOfWork();
            if (!$unitOfWork instanceof UnitOfWork) {
                throw new \LogicException('Expected Doctrine\ORM\UnitOfWork');
            }

            if ($unitOfWork->isInIdentityMap($object)) {
                $ids = $unitOfWork->getEntityIdentifier($object);
            } else {
                $ids = $objectManager->getClassMetadata($object::class)
                    ->getIdentifierValues($object);
            }
        } else {
            $ids = $objectManager->getClassMetadata($object::class)
                ->getIdentifierValues($object);
        }

        $stringIds = [];

        foreach ($ids as $id) {
            if (!\is_scalar($id) && !$id instanceof \Stringable) {
                throw new ObjectNotSupportedException($object);
            }

            $stringIds[] = (string) $id;
        }

        return implode('--', $stringIds);
    }
}
