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

use Rekalogika\File\Association\Contracts\ClassSignatureResolverInterface;
use Rekalogika\File\Tests\Tests\Model\Entity;
use Rekalogika\File\Tests\Tests\Model\EntityWithOverridenSignature;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class ClassSignatureTest extends KernelTestCase
{
    /**
     * @param class-string $class
     * @dataProvider classSignatureProvider
     */
    public function testClassSignature(
        string $class,
        string $expectedSignature,
    ): void {
        $classSignatureResolver = static::getContainer()
            ->get(ClassSignatureResolverInterface::class);

        $signature = $classSignatureResolver->getClassSignature($class);

        $this->assertSame($expectedSignature, $signature);
    }

    /**
     * @return iterable<array-key,array{class:class-string,expectedSignature:string}>
     */
    public static function classSignatureProvider(): iterable
    {
        yield [
            'class' => Entity::class,
            'expectedSignature' => 'bf4f1cf543bb2ff30f0db7ffb4af653fcf8292b7',
        ];

        yield [
            'class' => EntityWithOverridenSignature::class,
            'expectedSignature' => 'foo',
        ];
    }
}
