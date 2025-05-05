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

namespace Rekalogika\File\Tests\Tests\FileAssociation;

use PHPUnit\Framework\TestCase;
use Rekalogika\File\Association\ClassBasedFileLocationResolver\DefaultClassBasedFileLocationResolver;
use Rekalogika\File\Association\ClassSignatureResolver\DefaultClassSignatureResolver;
use Rekalogika\File\Association\ObjectIdResolver\DefaultObjectIdResolver;
use Rekalogika\File\Association\Util\ProxyUtil;
use Rekalogika\File\Tests\Tests\Model\Entity;

final class DefaultFileLocationResolverTest extends TestCase
{
    public function testDefaultLocationResolver(): void
    {
        $objectIdResolver = new DefaultObjectIdResolver();

        $locationResolver = new DefaultClassBasedFileLocationResolver(
            new DefaultClassSignatureResolver(),
        );

        $object = new Entity('entity_id');

        $location = $locationResolver->getFileLocation(
            class: ProxyUtil::normalizeClassName($object::class),
            id: $objectIdResolver->getObjectId($object),
            propertyName: 'file',
        );
        $this->assertSame('default', $location->getFilesystemIdentifier());
        $this->assertSame('entity/bf4f1cf543bb2ff30f0db7ffb4af653fcf8292b7/file/50/a7/74/1b/entity_id', $location->getKey());
    }
}
