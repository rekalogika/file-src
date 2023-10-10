/*
 * This file is part of rekalogika/file-src package.
 *
 * (c) Priyadi Iman Nurcahyo <https://rekalogika.dev>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

'use strict'

import { Controller } from '@hotwired/stimulus';

import * as FilePond from 'filepond'
import FilePondPluginFileEncode from 'filepond-plugin-file-encode'
import FilePondPluginFileMetadata from 'filepond-plugin-file-metadata'
import FilePondPluginFilePoster from 'filepond-plugin-file-poster'
import FilePondPluginFileValidateSize from 'filepond-plugin-file-validate-size'
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type'
import FilePondPluginImageCrop from 'filepond-plugin-image-crop'
import FilePondPluginImageEdit from 'filepond-plugin-image-edit'
import FilePondPluginImageExifOrientation from 'filepond-plugin-image-exif-orientation'
import FilePondPluginImagePreview from 'filepond-plugin-image-preview'
import FilePondPluginImageResize from 'filepond-plugin-image-resize'
import FilePondPluginImageTransform from 'filepond-plugin-image-transform'
import FilePondPluginImageValidateSize from 'filepond-plugin-image-validate-size'

import 'filepond/dist/filepond.min.css'
import 'filepond-plugin-file-poster/dist/filepond-plugin-file-poster.css'
import 'filepond-plugin-image-edit/dist/filepond-plugin-image-edit.css'
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css'

FilePond.registerPlugin(
    FilePondPluginFileEncode,
    FilePondPluginImageExifOrientation,
    FilePondPluginFileMetadata,
    FilePondPluginFilePoster,
    FilePondPluginFileValidateSize,
    FilePondPluginFileValidateType,
    FilePondPluginImageCrop,
    FilePondPluginImageEdit,
    FilePondPluginImagePreview,
    FilePondPluginImageResize,
    FilePondPluginImageTransform,
    FilePondPluginImageValidateSize
)

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    connect() {
        let files = []
        let input

        for (const child of this.element.children) {
            if (child.tagName === 'INPUT' && child.type === 'file') {
                input = child
            }
        }

        for (const child of this.element.children) {
            if (child.tagName !== 'DATA') {
                continue
            }

            let file = {
                // sentinel value sent to server. if this is value is sent
                // to the server, then the user didn't remove the image from
                // the filepond field
                source: '__NOT_DELETED__',

                options: {
                    type: 'local',

                    file: {
                        name: child.dataset.name,
                        size: child.dataset.size,
                        type: child.dataset.type
                    }
                }
            }

            if (child.dataset.href) {
                file.options.metadata = file.options.metadata || {}
                file.options.metadata.poster = child.dataset.href
            }

            files.push(file)
        }

        FilePond.create(input, {
            storeAsFile: true,
            files: files,
        })
    }
}

