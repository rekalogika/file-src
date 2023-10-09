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
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
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
                /** @var null|string */
                $data = $event->getData();

                // if the client sent NOT_DELETED_SENTINEL, it means the user
                // didn't remove the preview image from the filepond field. so,
                // we set the data from the existing value.

                if ($data == self::NOT_DELETED_SENTINEL) { // the image is not deleted by the user
                    $event->setData($event->getForm()->getData());
                    return;
                }

                // the client didn't send any data, most probably because they
                // removed the preview image from the upload box. if
                // 'remove_on_null' is off, we set the data using the existing
                // data. if 'remove_on_null' is on, then the data remains null,
                // and the existing file (if exists) will be removed by doctrine

                if (!$data) {
                    if (!$options['remove_on_null']) {
                        $event->setData($event->getForm()->getData());
                    }
                    return;
                }

                // save the file, and add it as our data

                $file = FilePondFileEncodeAdapter::adapt($data);
                $event->setData($file);
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
            'remove_on_null' => false,
            'data_class' => FileInterface::class,
        ]);
    }
}
