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

namespace Rekalogika\File\Bridge\Symfony\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType as SymfonyFileType;
use Symfony\Component\Form\FormBuilderInterface;

final class FileType extends AbstractType
{
    #[\Override]
    public function getParent(): string
    {
        return SymfonyFileType::class;
    }

    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(new FileTransformer());
    }
}
