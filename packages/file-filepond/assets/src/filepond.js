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

// locales

import ar from 'filepond/locale/ar-ar.js'
import cs from 'filepond/locale/cs-cz.js'
import da from 'filepond/locale/da-dk.js'
import de from 'filepond/locale/de-de.js'
import el from 'filepond/locale/el-el.js'
import en from 'filepond/locale/en-en.js'
import es from 'filepond/locale/es-es.js'
import fa from 'filepond/locale/fa_ir.js'
import fi from 'filepond/locale/fi-fi.js'
import fr from 'filepond/locale/fr-fr.js'
import he from 'filepond/locale/he-he.js'
import hr from 'filepond/locale/hr-hr.js'
import hu from 'filepond/locale/hu-hu.js'
import id from 'filepond/locale/id-id.js'
import it from 'filepond/locale/it-it.js'
import ja from 'filepond/locale/ja-ja.js'
import lt from 'filepond/locale/lt-lt.js'
import nl from 'filepond/locale/nl-nl.js'
import no from 'filepond/locale/no_nb.js'
import pl from 'filepond/locale/pl-pl.js'
import pt from 'filepond/locale/pt-br.js'
import ro from 'filepond/locale/ro-ro.js'
import ru from 'filepond/locale/ru-ru.js'
import sk from 'filepond/locale/sk-sk.js'
import sv from 'filepond/locale/sv_se.js'
import tr from 'filepond/locale/tr-tr.js'
import uk from 'filepond/locale/uk-ua.js'
import vi from 'filepond/locale/vi-vi.js'
import zh_cn from 'filepond/locale/zh-cn.js'
import zh_tw from 'filepond/locale/zh-tw.js'

const locales = {
    'ar': ar,
    'cs': cs,
    'da': da,
    'de': de,
    'el': el,
    'en': en,
    'es': es,
    'fa': fa,
    'fi': fi,
    'fr': fr,
    'he': he,
    'hr': hr,
    'hu': hu,
    'id': id,
    'it': it,
    'ja': ja,
    'lt': lt,
    'nl': nl,
    'no': no,
    'pl': pl,
    'pt': pt,
    'ro': ro,
    'ru': ru,
    'sk': sk,
    'sv': sv,
    'tr': tr,
    'uk': uk,
    'vi': vi,
    'zh-cn': zh_cn,
    'zh-tw': zh_tw,
}

function getCurrentLocale() {
    let locale = document.getElementsByTagName("html")[0].getAttribute("lang")

    if (!locale) {
        locale = document.documentElement.lang
    }

    if (locale.length > 2) {
        locale = locale.replace('_', '-').toLowerCase

        if (locale != 'zh-cn' && locale != 'zh-tw') {
            locale = locale.substring(0, 2)
        }
    }

    if (locales[locale]) {
        return locales[locale]
    } else {
        return en
    }
}

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
            ...getCurrentLocale(),
            storeAsFile: true,
            files: files,
        })
    }
}

