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
 * A FileType utilizing FilePond
 */
class FilePondType extends FileType
{
    public const NOT_DELETED_SENTINEL = '__NOT_DELETED__';

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

                // if the client sent NOT_DELETED_SENTINEL, it means the user
                // didn't remove the preview image from the filepond field. so,
                // we set the data from the existing value.

                if (
                    (is_string($data) && $data == self::NOT_DELETED_SENTINEL)
                    ||
                    ($data instanceof FileInterface
                        && $data->getSize() == strlen(self::NOT_DELETED_SENTINEL)
                        && $data->getContent() == self::NOT_DELETED_SENTINEL)
                ) {
                    $event->setData($event->getForm()->getData());
                    return;
                }

                // the client did not send any data, because they file did not
                // exist in the first place, or because the user has removed the
                // preview image from the upload box. if 'allow_delete' is
                // off, we set the data using the existing data. if
                // 'allow_delete' is on, then the data remains null, and the
                // existing file (if exists) will be removed by doctrine

                if (!$data) {
                    if (!$options['allow_delete']) {
                        $event->setData($event->getForm()->getData());
                    }
                    return;
                }

                // save the file, and add it as our data

                if (is_string($data)) {
                    $data = FilePondFileEncodeAdapter::adapt($data);
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
