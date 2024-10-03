/*
 * This file is part of rekalogika/file-src package.
 *
 * (c) Priyadi Iman Nurcahyo <https://rekalogika.dev>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

'use strict';

function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }
function _createForOfIteratorHelperLoose(r, e) { var t = "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"]; if (t) return (t = t.call(r)).next.bind(t); if (Array.isArray(r) || (t = _unsupportedIterableToArray(r)) || e && r && "number" == typeof r.length) { t && (r = t); var o = 0; return function () { return o >= r.length ? { done: !0 } : { done: !1, value: r[o++] }; }; } throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
function _callSuper(t, o, e) { return o = _getPrototypeOf(o), _possibleConstructorReturn(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], _getPrototypeOf(t).constructor) : o.apply(t, e)); }
function _possibleConstructorReturn(t, e) { if (e && ("object" == typeof e || "function" == typeof e)) return e; if (void 0 !== e) throw new TypeError("Derived constructors may only return object or undefined"); return _assertThisInitialized(t); }
function _assertThisInitialized(e) { if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); return e; }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function _getPrototypeOf(t) { return _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function (t) { return t.__proto__ || Object.getPrototypeOf(t); }, _getPrototypeOf(t); }
function _inheritsLoose(t, o) { t.prototype = Object.create(o.prototype), t.prototype.constructor = t, _setPrototypeOf(t, o); }
function _setPrototypeOf(t, e) { return _setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function (t, e) { return t.__proto__ = e, t; }, _setPrototypeOf(t, e); }
import { Controller } from '@hotwired/stimulus';
import * as FilePond from 'filepond';
import FilePondPluginFileEncode from 'filepond-plugin-file-encode';
import FilePondPluginFileMetadata from 'filepond-plugin-file-metadata';
import FilePondPluginFilePoster from 'filepond-plugin-file-poster';
import FilePondPluginFileValidateSize from 'filepond-plugin-file-validate-size';
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type';
import FilePondPluginImageCrop from 'filepond-plugin-image-crop';
import FilePondPluginImageEdit from 'filepond-plugin-image-edit';
import FilePondPluginImageExifOrientation from 'filepond-plugin-image-exif-orientation';
import FilePondPluginImagePreview from 'filepond-plugin-image-preview';
import FilePondPluginImageResize from 'filepond-plugin-image-resize';
import FilePondPluginImageTransform from 'filepond-plugin-image-transform';
import FilePondPluginImageValidateSize from 'filepond-plugin-image-validate-size';
import 'filepond/dist/filepond.min.css';
import 'filepond-plugin-file-poster/dist/filepond-plugin-file-poster.css';
import 'filepond-plugin-image-edit/dist/filepond-plugin-image-edit.css';
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css';

// locales

import ar from 'filepond/locale/ar-ar.js';
import cs from 'filepond/locale/cs-cz.js';
import da from 'filepond/locale/da-dk.js';
import de from 'filepond/locale/de-de.js';
import el from 'filepond/locale/el-el.js';
import en from 'filepond/locale/en-en.js';
import es from 'filepond/locale/es-es.js';
import fa from 'filepond/locale/fa_ir.js';
import fi from 'filepond/locale/fi-fi.js';
import fr from 'filepond/locale/fr-fr.js';
import he from 'filepond/locale/he-he.js';
import hr from 'filepond/locale/hr-hr.js';
import hu from 'filepond/locale/hu-hu.js';
import id from 'filepond/locale/id-id.js';
import it from 'filepond/locale/it-it.js';
import ja from 'filepond/locale/ja-ja.js';
import lt from 'filepond/locale/lt-lt.js';
import nl from 'filepond/locale/nl-nl.js';
import no from 'filepond/locale/no_nb.js';
import pl from 'filepond/locale/pl-pl.js';
import pt from 'filepond/locale/pt-br.js';
import ro from 'filepond/locale/ro-ro.js';
import ru from 'filepond/locale/ru-ru.js';
import sk from 'filepond/locale/sk-sk.js';
import sv from 'filepond/locale/sv_se.js';
import tr from 'filepond/locale/tr-tr.js';
import uk from 'filepond/locale/uk-ua.js';
import vi from 'filepond/locale/vi-vi.js';
import zh_cn from 'filepond/locale/zh-cn.js';
import zh_tw from 'filepond/locale/zh-tw.js';
var locales = {
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
  'zh-tw': zh_tw
};
function getCurrentLocale() {
  var locale = document.getElementsByTagName("html")[0].getAttribute("lang");
  if (!locale) {
    locale = document.documentElement.lang;
  }
  if (locale.length > 2) {
    locale = locale.replace('_', '-').toLowerCase;
    if (locale != 'zh-cn' && locale != 'zh-tw') {
      locale = locale.substring(0, 2);
    }
  }
  if (locales[locale]) {
    return locales[locale];
  } else {
    return en;
  }
}
FilePond.registerPlugin(FilePondPluginFileEncode, FilePondPluginImageExifOrientation, FilePondPluginFileMetadata, FilePondPluginFilePoster, FilePondPluginFileValidateSize, FilePondPluginFileValidateType, FilePondPluginImageCrop, FilePondPluginImageEdit, FilePondPluginImagePreview, FilePondPluginImageResize, FilePondPluginImageTransform, FilePondPluginImageValidateSize);

/* stimulusFetch: 'lazy' */
var _default = /*#__PURE__*/function (_Controller) {
  function _default() {
    return _callSuper(this, _default, arguments);
  }
  _inheritsLoose(_default, _Controller);
  var _proto = _default.prototype;
  _proto.connect = function connect() {
    var files = [];
    var input;

    // get the file input element
    for (var _iterator = _createForOfIteratorHelperLoose(this.element.children), _step; !(_step = _iterator()).done;) {
      var child = _step.value;
      if (child.tagName === 'INPUT' && child.type === 'file') {
        input = child;
      }
    }

    // remove input class attribute
    input.removeAttribute('class');

    // remove input style attribute
    input.removeAttribute('style');

    // get the files from the data elements and process them
    for (var _iterator2 = _createForOfIteratorHelperLoose(this.element.children), _step2; !(_step2 = _iterator2()).done;) {
      var _child = _step2.value;
      if (_child.tagName !== 'DATA') {
        continue;
      }
      var file = {
        // sentinel value sent to server. if this is value is sent
        // to the server, then the user has not removed the image from
        // the filepond field
        source: _child.dataset.id || '__NOT_DELETED__',
        options: {
          type: 'local',
          file: {
            name: _child.dataset.name,
            size: _child.dataset.size,
            type: _child.dataset.type
          }
        }
      };
      if (_child.dataset.href) {
        file.options.metadata = file.options.metadata || {};
        file.options.metadata.poster = _child.dataset.href;
      }
      files.push(file);
    }

    // create filepond instance
    FilePond.create(input, _extends({}, getCurrentLocale(), {
      storeAsFile: true,
      files: files
    }));
  };
  return _default;
}(Controller);
export { _default as default };