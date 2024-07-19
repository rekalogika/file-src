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

namespace Rekalogika\File\Bridge\FilePond;

use Rekalogika\Contracts\File\FileInterface;
use Rekalogika\Domain\File\Association\Entity\FileCollection;
use Rekalogika\File\Bridge\Symfony\HttpFoundation\FromHttpFoundationFileAdapter;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form type for handling multiple files.
 */
class FilePondCollectionType extends FileType
{
    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['multiple'] !== true) {
            throw new \InvalidArgumentException('FilePondCollectionType only accept "multiple" option');
        }

        $builder
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($options): void {
                $incomingFiles = $event->getData();
                if (!is_array($incomingFiles)) {
                    throw new \InvalidArgumentException('Incoming files must be an array');
                }

                $entityFiles = $event->getForm()->getData();
                if (!$entityFiles instanceof FileCollection) {
                    throw new \InvalidArgumentException('FilePondCollectionType only supports FileCollection');
                }

                $newData = $entityFiles->toArray();

                if ($options['allow_delete'] === true) {
                    foreach (array_keys($newData) as $key) {
                        if (!in_array((string) $key, $incomingFiles, true)) {
                            unset($newData[$key]);
                        }
                    }
                }

                /** @var array<array-key,UploadedFile|FileInterface|string> $incomingFiles */

                foreach ($incomingFiles as $file) {
                    if ($file instanceof UploadedFile) {
                        $file = FromHttpFoundationFileAdapter::adapt($file);
                    }

                    if (\is_string($file)) {
                        // try decoding if the client sent a base64 string
                        try {
                            $file = FilePondFileEncodeAdapter::adaptFromString($file);
                        } catch (\JsonException) {
                            // if the client sent a plain string, it means the user
                            // did not delete the file that is already existing
                            continue;
                        }
                    }

                    if (!$file instanceof FileInterface) {
                        throw new \InvalidArgumentException('Invalid file');
                    }

                    $newData[] = $file;
                }

                $event->setData($newData);
            })
        ;
    }

    #[\Override]
    public function getBlockPrefix(): string
    {
        return 'rekalogika_file_filepond_collection';
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'allow_delete' => false,
            'multiple' => true,
        ]);
    }
}
