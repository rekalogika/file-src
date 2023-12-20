/*
 * This file is part of rekalogika/file-src package.
 *
 * (c) Priyadi Iman Nurcahyo <https://rekalogika.dev>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

'use strict';

function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;
var _stimulus = require("@hotwired/stimulus");
var FilePond = _interopRequireWildcard(require("filepond"));
var _filepondPluginFileEncode = _interopRequireDefault(require("filepond-plugin-file-encode"));
var _filepondPluginFileMetadata = _interopRequireDefault(require("filepond-plugin-file-metadata"));
var _filepondPluginFilePoster = _interopRequireDefault(require("filepond-plugin-file-poster"));
var _filepondPluginFileValidateSize = _interopRequireDefault(require("filepond-plugin-file-validate-size"));
var _filepondPluginFileValidateType = _interopRequireDefault(require("filepond-plugin-file-validate-type"));
var _filepondPluginImageCrop = _interopRequireDefault(require("filepond-plugin-image-crop"));
var _filepondPluginImageEdit = _interopRequireDefault(require("filepond-plugin-image-edit"));
var _filepondPluginImageExifOrientation = _interopRequireDefault(require("filepond-plugin-image-exif-orientation"));
var _filepondPluginImagePreview = _interopRequireDefault(require("filepond-plugin-image-preview"));
var _filepondPluginImageResize = _interopRequireDefault(require("filepond-plugin-image-resize"));
var _filepondPluginImageTransform = _interopRequireDefault(require("filepond-plugin-image-transform"));
var _filepondPluginImageValidateSize = _interopRequireDefault(require("filepond-plugin-image-validate-size"));
require("filepond/dist/filepond.min.css");
require("filepond-plugin-file-poster/dist/filepond-plugin-file-poster.css");
require("filepond-plugin-image-edit/dist/filepond-plugin-image-edit.css");
require("filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css");
var _arAr = _interopRequireDefault(require("filepond/locale/ar-ar.js"));
var _csCz = _interopRequireDefault(require("filepond/locale/cs-cz.js"));
var _daDk = _interopRequireDefault(require("filepond/locale/da-dk.js"));
var _deDe = _interopRequireDefault(require("filepond/locale/de-de.js"));
var _elEl = _interopRequireDefault(require("filepond/locale/el-el.js"));
var _enEn = _interopRequireDefault(require("filepond/locale/en-en.js"));
var _esEs = _interopRequireDefault(require("filepond/locale/es-es.js"));
var _fa_ir = _interopRequireDefault(require("filepond/locale/fa_ir.js"));
var _fiFi = _interopRequireDefault(require("filepond/locale/fi-fi.js"));
var _frFr = _interopRequireDefault(require("filepond/locale/fr-fr.js"));
var _heHe = _interopRequireDefault(require("filepond/locale/he-he.js"));
var _hrHr = _interopRequireDefault(require("filepond/locale/hr-hr.js"));
var _huHu = _interopRequireDefault(require("filepond/locale/hu-hu.js"));
var _idId = _interopRequireDefault(require("filepond/locale/id-id.js"));
var _itIt = _interopRequireDefault(require("filepond/locale/it-it.js"));
var _jaJa = _interopRequireDefault(require("filepond/locale/ja-ja.js"));
var _ltLt = _interopRequireDefault(require("filepond/locale/lt-lt.js"));
var _nlNl = _interopRequireDefault(require("filepond/locale/nl-nl.js"));
var _no_nb = _interopRequireDefault(require("filepond/locale/no_nb.js"));
var _plPl = _interopRequireDefault(require("filepond/locale/pl-pl.js"));
var _ptBr = _interopRequireDefault(require("filepond/locale/pt-br.js"));
var _roRo = _interopRequireDefault(require("filepond/locale/ro-ro.js"));
var _ruRu = _interopRequireDefault(require("filepond/locale/ru-ru.js"));
var _skSk = _interopRequireDefault(require("filepond/locale/sk-sk.js"));
var _sv_se = _interopRequireDefault(require("filepond/locale/sv_se.js"));
var _trTr = _interopRequireDefault(require("filepond/locale/tr-tr.js"));
var _ukUa = _interopRequireDefault(require("filepond/locale/uk-ua.js"));
var _viVi = _interopRequireDefault(require("filepond/locale/vi-vi.js"));
var _zhCn = _interopRequireDefault(require("filepond/locale/zh-cn.js"));
var _zhTw = _interopRequireDefault(require("filepond/locale/zh-tw.js"));
function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }
function _getRequireWildcardCache(e) { if ("function" != typeof WeakMap) return null; var r = new WeakMap(), t = new WeakMap(); return (_getRequireWildcardCache = function _getRequireWildcardCache(e) { return e ? t : r; })(e); }
function _interopRequireWildcard(e, r) { if (!r && e && e.__esModule) return e; if (null === e || "object" != _typeof(e) && "function" != typeof e) return { "default": e }; var t = _getRequireWildcardCache(r); if (t && t.has(e)) return t.get(e); var n = { __proto__: null }, a = Object.defineProperty && Object.getOwnPropertyDescriptor; for (var u in e) if ("default" !== u && Object.prototype.hasOwnProperty.call(e, u)) { var i = a ? Object.getOwnPropertyDescriptor(e, u) : null; i && (i.get || i.set) ? Object.defineProperty(n, u, i) : n[u] = e[u]; } return n["default"] = e, t && t.set(e, n), n; }
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { _defineProperty(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
function _defineProperty(obj, key, value) { key = _toPropertyKey(key); if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }
function _createForOfIteratorHelper(o, allowArrayLike) { var it = typeof Symbol !== "undefined" && o[Symbol.iterator] || o["@@iterator"]; if (!it) { if (Array.isArray(o) || (it = _unsupportedIterableToArray(o)) || allowArrayLike && o && typeof o.length === "number") { if (it) o = it; var i = 0; var F = function F() {}; return { s: F, n: function n() { if (i >= o.length) return { done: true }; return { done: false, value: o[i++] }; }, e: function e(_e) { throw _e; }, f: F }; } throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); } var normalCompletion = true, didErr = false, err; return { s: function s() { it = it.call(o); }, n: function n() { var step = it.next(); normalCompletion = step.done; return step; }, e: function e(_e2) { didErr = true; err = _e2; }, f: function f() { try { if (!normalCompletion && it["return"] != null) it["return"](); } finally { if (didErr) throw err; } } }; }
function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }
function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) arr2[i] = arr[i]; return arr2; }
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }
function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, _toPropertyKey(descriptor.key), descriptor); } }
function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, "prototype", { writable: false }); return Constructor; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : String(i); }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); Object.defineProperty(subClass, "prototype", { writable: false }); if (superClass) _setPrototypeOf(subClass, superClass); }
function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }
function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function _createSuperInternal() { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }
function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } else if (call !== void 0) { throw new TypeError("Derived constructors may only return object or undefined"); } return _assertThisInitialized(self); }
function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }
function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }
function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); } // locales
var locales = {
  'ar': _arAr["default"],
  'cs': _csCz["default"],
  'da': _daDk["default"],
  'de': _deDe["default"],
  'el': _elEl["default"],
  'en': _enEn["default"],
  'es': _esEs["default"],
  'fa': _fa_ir["default"],
  'fi': _fiFi["default"],
  'fr': _frFr["default"],
  'he': _heHe["default"],
  'hr': _hrHr["default"],
  'hu': _huHu["default"],
  'id': _idId["default"],
  'it': _itIt["default"],
  'ja': _jaJa["default"],
  'lt': _ltLt["default"],
  'nl': _nlNl["default"],
  'no': _no_nb["default"],
  'pl': _plPl["default"],
  'pt': _ptBr["default"],
  'ro': _roRo["default"],
  'ru': _ruRu["default"],
  'sk': _skSk["default"],
  'sv': _sv_se["default"],
  'tr': _trTr["default"],
  'uk': _ukUa["default"],
  'vi': _viVi["default"],
  'zh-cn': _zhCn["default"],
  'zh-tw': _zhTw["default"]
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
    return _enEn["default"];
  }
}
FilePond.registerPlugin(_filepondPluginFileEncode["default"], _filepondPluginImageExifOrientation["default"], _filepondPluginFileMetadata["default"], _filepondPluginFilePoster["default"], _filepondPluginFileValidateSize["default"], _filepondPluginFileValidateType["default"], _filepondPluginImageCrop["default"], _filepondPluginImageEdit["default"], _filepondPluginImagePreview["default"], _filepondPluginImageResize["default"], _filepondPluginImageTransform["default"], _filepondPluginImageValidateSize["default"]);

/* stimulusFetch: 'lazy' */
var _default = exports["default"] = /*#__PURE__*/function (_Controller) {
  _inherits(_default, _Controller);
  var _super = _createSuper(_default);
  function _default() {
    _classCallCheck(this, _default);
    return _super.apply(this, arguments);
  }
  _createClass(_default, [{
    key: "connect",
    value: function connect() {
      var files = [];
      var input;
      var _iterator = _createForOfIteratorHelper(this.element.children),
        _step;
      try {
        for (_iterator.s(); !(_step = _iterator.n()).done;) {
          var child = _step.value;
          if (child.tagName === 'INPUT' && child.type === 'file') {
            input = child;
          }
        }
      } catch (err) {
        _iterator.e(err);
      } finally {
        _iterator.f();
      }
      var _iterator2 = _createForOfIteratorHelper(this.element.children),
        _step2;
      try {
        for (_iterator2.s(); !(_step2 = _iterator2.n()).done;) {
          var _child = _step2.value;
          if (_child.tagName !== 'DATA') {
            continue;
          }
          var file = {
            // sentinel value sent to server. if this is value is sent
            // to the server, then the user didn't remove the image from
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
      } catch (err) {
        _iterator2.e(err);
      } finally {
        _iterator2.f();
      }
      FilePond.create(input, _objectSpread(_objectSpread({}, getCurrentLocale()), {}, {
        storeAsFile: true,
        files: files
      }));
    }
  }]);
  return _default;
}(_stimulus.Controller);