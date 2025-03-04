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

namespace Rekalogika\File\Tests\Tests\FileSymfonyBridge;

use Rekalogika\File\Bridge\Symfony\Constraints\Image;
use Rekalogika\File\Bridge\Symfony\Constraints\ImageValidator;
use Rekalogika\File\File;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * @extends ConstraintValidatorTestCase<ImageValidator>
 */
final class ImageValidatorTest extends ConstraintValidatorTestCase
{
    #[\Override]
    protected function createValidator(): ConstraintValidatorInterface
    {
        return new ImageValidator();
    }

    public function testMaxWidth(): void
    {
        $image = new File(__DIR__ . '/../Resources/smiley.png');
        $constraint = new Image(maxWidth: 2000);
        $this->validator->validate($image, $constraint);
        $this->assertNoViolation();
    }

    public function testExceedingMaxWidth(): void
    {
        $image = new File(__DIR__ . '/../Resources/smiley.png');
        $constraint = new Image(maxWidth: 10);
        $this->validator->validate($image, $constraint);

        /** @var string */
        $maxWidth = $constraint->maxWidthMessage;

        $this->buildViolation($maxWidth)
            ->setParameter('{{ width }}', '240')
            ->setParameter('{{ max_width }}', '10')
            ->setCode(Image::TOO_WIDE_ERROR)
            ->assertRaised();
    }
}
