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
use Rekalogika\File\Bridge\Symfony\HttpFoundation\FromHttpFoundationFileAdapter;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * A FileType utilizing FilePond.
 *
 * FilePond supports two mode of operation:
 * 1. Using standard multipart/form-data encoding
 * 2. By encoding the file as a base64 string and sending it as a JSON object
 *
 * This class supports both modes of operation. By default it uses
 * multipart/form-data, to use the base64 string encoding, add this attributes:
 *
 * ```
 * 'attr' => [
 *     'data-allow-file-encode' => 'true',
 *     'data-store-as-file' => 'false',
 * ],
 * ```
 */
class FilePondType extends FileType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['multiple'] === true) {
            throw new \InvalidArgumentException('FilePondType does not support multiple files');
        }

        $builder
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($options) {
                /** @var null|string|UploadedFile|FileInterface */
                $data = $event->getData();
                if ($data instanceof UploadedFile) {
                    $data = FromHttpFoundationFileAdapter::adapt($data);
                }

                if (\is_string($data)) {
                    // try decoding if the client sent a base64 string
                    try {
                        $data = FilePondFileEncodeAdapter::adaptFromString($data);
                    } catch (\JsonException $e) {
                        // if the client sent a plain string, it means the user
                        // did not delete the file that is already existing
                        $event->setData($event->getForm()->getData());
                        return;
                    }
                } elseif (!$data) {
                    // the client did not send any data, because they file did
                    // not exist in the first place, or because the user has
                    // removed the preview image from the upload box. if
                    // 'allow_delete' is off, we set the data using the existing
                    // data. if 'allow_delete' is on, then the data remains
                    // null, and the existing file (if exists) will be removed
                    // by doctrine
                    if (!$options['allow_delete']) {
                        $event->setData($event->getForm()->getData());
                    }
                    return;
                }

                $event->setData($data);
            });
    }

    public function getBlockPrefix(): string
    {
        return 'rekalogika_file_filepond';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'allow_delete' => false,
            'data_class' => FileInterface::class,
        ]);
    }
}
