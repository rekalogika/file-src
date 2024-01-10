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

namespace Rekalogika\File\Tests\FileSymfonyBridge;

use Rekalogika\File\Bridge\Symfony\Constraints\File;
use Rekalogika\File\Bridge\Symfony\Constraints\FileValidator;
use Rekalogika\File\TemporaryFile;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * @extends ConstraintValidatorTestCase<FileValidator>
 */
class FileValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator(): ConstraintValidatorInterface
    {
        return new FileValidator();
    }

    public function testMaxsize(): void
    {
        $file = TemporaryFile::createFromString('test');

        $this->validator->validate($file, new File(maxSize: '1k'));
        $this->assertNoViolation();
    }

    public function testExceedingMaxsize(): void
    {
        $file = TemporaryFile::createFromString('test');
        $constraint = new File(maxSize: '1');
        $this->validator->validate($file, $constraint);

        $violation = $this->context->getViolations()->get(0);
        $file = (string) $violation->getParameters()['{{ file }}'];
        $name = (string) $violation->getParameters()['{{ name }}'];

        /** @var string */
        $maxSizeMessage = $constraint->maxSizeMessage;

        $this->buildViolation($maxSizeMessage)
            ->setParameter('{{ limit }}', '1')
            ->setParameter('{{ size }}', '4')
            ->setParameter('{{ file }}', $file)
            ->setParameter('{{ name }}', $name)
            ->setParameter('{{ suffix }}', 'bytes')
            ->setCode(File::TOO_LARGE_ERROR)
            ->assertRaised();
    }
}
