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
use Rekalogika\File\Association\Contracts\PropertyListerInterface;
use Rekalogika\File\Association\Model\Property;
use Rekalogika\File\Association\PropertyLister\AttributesPropertyLister;
use Rekalogika\File\Association\PropertyLister\FileAssociationInterfacePropertyLister;
use Rekalogika\File\Tests\Tests\Model\EntityImplementingFileAssociation;
use Rekalogika\File\Tests\Tests\Model\EntityWithAttribute;
use Rekalogika\File\Tests\Tests\Model\SubclassOfEntityImplementingFileAssociation;
use Rekalogika\File\Tests\Tests\Model\SubclassOfEntityWithAttribute;

final class PropertyListerTest extends TestCase
{
    /**
     * @param class-string $class
     * @param list<Property> $expectedProperties
     * @dataProvider propertyListerProvider
     */
    public function testPropertyLister(
        PropertyListerInterface $lister,
        string $class,
        array $expectedProperties,
        bool $invalid,
    ): void {
        if ($invalid) {
            $this->expectException(\LogicException::class);
        }

        $properties = $lister->getFileProperties($class);

        $properties = $properties instanceof \Traversable
            ? iterator_to_array($properties)
            : $properties;

        $this->assertEqualsCanonicalizing($expectedProperties, $properties);
    }

    /**
     * @return iterable<array-key,array{PropertyListerInterface,class-string,list<Property>}>
     */
    public static function propertyListerProvider(): iterable
    {
        yield [
            new AttributesPropertyLister(),
            EntityWithAttribute::class,
            [
                new Property(
                    class: EntityWithAttribute::class,
                    name: 'file',
                ),
                new Property(
                    class: EntityWithAttribute::class,
                    name: 'protectedFile',
                ),
            ],
            false,
        ];

        yield [
            new AttributesPropertyLister(),
            SubclassOfEntityWithAttribute::class,
            [
                new Property(
                    class: SubclassOfEntityWithAttribute::class,
                    name: 'anotherFile',
                ),
                new Property(
                    class: EntityWithAttribute::class,
                    name: 'file',
                ),
                new Property(
                    class: SubclassOfEntityWithAttribute::class,
                    name: 'file',
                ),
                new Property(
                    class: SubclassOfEntityWithAttribute::class,
                    name: 'protectedFile',
                ),
            ],
            true,
        ];

        yield [
            new FileAssociationInterfacePropertyLister(),
            EntityImplementingFileAssociation::class,
            [
                new Property(
                    class: EntityImplementingFileAssociation::class,
                    name: 'file',
                ),
                new Property(
                    class: EntityImplementingFileAssociation::class,
                    name: 'protectedFile',
                ),
            ],
            false,
        ];

        yield [
            new FileAssociationInterfacePropertyLister(),
            SubclassOfEntityImplementingFileAssociation::class,
            [
                new Property(
                    class: SubclassOfEntityImplementingFileAssociation::class,
                    name: 'anotherFile',
                ),
                new Property(
                    class: EntityImplementingFileAssociation::class,
                    name: 'file',
                ),
                new Property(
                    class: SubclassOfEntityImplementingFileAssociation::class,
                    name: 'file',
                ),
                new Property(
                    class: SubclassOfEntityImplementingFileAssociation::class,
                    name: 'protectedFile',
                ),
            ],
            true,
        ];
    }
}
