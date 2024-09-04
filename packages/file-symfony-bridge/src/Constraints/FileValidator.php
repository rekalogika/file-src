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

namespace Rekalogika\File\Bridge\Symfony\Constraints;

use Rekalogika\Contracts\File\FileInterface;
use Rekalogika\File\Bridge\Symfony\HttpFoundation\ToHttpFoundationFileAdapter;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\FileValidator as SymfonyFileValidator;

class FileValidator extends SymfonyFileValidator
{
    #[\Override]
    public function validate(mixed $value, Constraint $constraint): void
    {
        if ($value instanceof FileInterface) {
            $value = ToHttpFoundationFileAdapter::adapt($value);
        }

        parent::validate($value, $constraint);
    }
}
