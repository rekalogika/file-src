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

use Rekalogika\File\Association\Contracts\ObjectIdResolverInterface;
use Rekalogika\File\Association\Exception\ObjectIdResolver\EmptyIdException;
use Rekalogika\File\Association\Exception\ObjectIdResolver\IdNotSupportedException;
use Rekalogika\File\Association\Exception\ObjectIdResolver\MethodNotFoundException;

final readonly class DefaultObjectIdResolver implements ObjectIdResolverInterface
{
    public function __construct(
        private string $method = 'getId',
    ) {}

    #[\Override]
    public function getObjectId(object $object): string
    {
        if (method_exists($object, $this->method)) {
            /** @var mixed */
            $id = $object->{$this->method}();
        } else {
            throw new MethodNotFoundException($object, $this->method);
        }

        if (!\is_string($id) && !\is_int($id) && !$id instanceof \Stringable) {
            throw new IdNotSupportedException($object, $this->method, $id);
        }

        $id = (string) $id;

        if ($id === '') {
            throw new EmptyIdException($object, $this->method);
        }

        return $id;
    }
}
