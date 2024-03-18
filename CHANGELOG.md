# CHANGELOG

## 1.8.3

* fix: inconsistent intervention version

## 1.8.2

* build: allow specifying option in `make phpunit`.
* fix: fix errors under `open_basedir` restrictions.

## 1.8.0

* Supports Symfony 7

## 1.7.3

* Use lazy-loading in `FileTrait`.
* Add informative exception if the file is missing in `FileTrait`.
* Use `UnsetFile` in place of an unset file. Allows caller to get data from the
  embedded metadata if the file is unset. Will only throw exception, i.e. if the
  caller attempts to get the file.

## 1.7.2

* Fix typehints
* Update Filepond JS dependencies
* Fix common extensions tests
* Update psalm (fixes Override)
* Remove allowFileEncode in FilePondCollectionType as upstream already fixed the
  blocker

## 1.7.1

* Change version dependency of `psr/http-message` to `^1.0 || ^2.0`

## 1.7.0

* Bump deps of `symfony/form` to `^6.3.6 || ^6.4` because of the [necessary
  fixes](https://github.com/symfony/symfony/pull/52021)
* Update for config deprecations in preparation for Symfony 7
* Add `FilePondCollectionType`.
* Revert `symfony/form` dep to `6.2` because we are blocked by a [FilePond
  bug](https://github.com/pqina/filepond/pull/941) anyway.
* Force `FilePondCollectionType` to use `allowFileEncode` and disable
  `storeAsFile` for now.

## 1.6.2

* filepond: Now depends on `file-server`

## 1.6.1

* filepond: Rename `remove_on_null` to `allow_delete` for consistency with other
  form types.
* symfony-bridge: Update `FileTransformer` to deal with situations where the
  transformer is installed twice on the same form. For example, if
  `FileTypeExtension` is active app-wide.

## 1.6.0

* Add FilePond form type.

## 1.5.6

* Fix passing translation in ZIP file name.

## 1.5.5

* `FileCollection` and `ReadableFileCollection` now accept a translated name.
* Add `TranslatableFileName`

## 1.5.4

* Update doctrine-collections-decorator to 2.0

## 1.5.3

* `file-bundle` now suggests `file-zip`.
* Fix type hint for `FileInterface::get()`

## 1.5.2

* Add `ReadableFileCollection`. Rename `FileCollectionDecorator` to
  `FileCollection`.
* Add `Directory`, which is an ultra-simple implementation of
  `DirectoryInterface`.
* Add `FileZip::createZipResponse()`

## 1.5.1

* Rename `FilesCollection` to `FileCollectionDecorator`

## 1.5.0

* Interfaces to implement a tree-like structure for `FileInterface` and
  `FilePointerInterface`. Add `NodeInterface` & `DirectoryInterface`. Should
  still be backward compatible.
* Zip-streaming of `DirectoryInterface`.

## 1.4.3

* Simplify exception handling in `NullFileTrait`
* Fix "uncaught exception" if the `$other` field is null in the database.

## 1.4.2

* Add `DoctrineObjectIdResolver` for a more universal object id resolver.
* Cache object id in `ChainedObjectIdResolver`

## 1.4.1

* Add `FileDecorator::setFileMandatory()` to deal with non-nullable file
  properties in entities.
* Make file properties in `FileTrait` non-nullable.
* `PropertyInspector`: fix bug where the property inspector would not find
  private properties in parent classes.
## 1.4.0

* Add `PropertyInspectorInterface` in `file-association`
* Implements lazy-loading of files in entities.
* Create `file-null` package to hold null file objects.
* Remove `NullFile` from `file-association-entity`.
* Add `NullFileInterface` & `NullFilePointerInterface` to the contracts.
* If the file property in an entity is not nullable, substitute it with a
  `MissingFile` object.

## 1.3.1

* Spin off the main part of `AbstractFile` into `FileTrait` to cater to entities
  that have to extend another class.
* `FileDecoratorTrait`: change `getWrapped()` visibility to private

## 1.3.0

* Separate metadata classes from `rekalogika/file` to `rekalogika/file-metadata`.
* Add `rekalogika/file-association-entity` to allow replicating metadata inside Doctrine entities.
* Add `AbstractFile` to help create file entities (and collection of files).
* Add `NullFile` to represent a file that should exist but does not.
## 1.2.0

* Remove remnant.
* Change `RawMetadataInterface::get()` to throw `MetadataNotFoundException` if the key is not found.
* Add `RawMetadataInterface::tryGet()` to return `null` if the key is not found.

## 1.1.0

* Change translated return type from `string|(\Stringable&TranslatableInterface)` to `\Stringable&TranslatableInterface`
* Allow PHP 8.1

## 1.0.1

* Fix translation directory location

## 1.0.0

* Refactor translation

## 0.1.4

* `AttributesPropertyLister` now caches the results in memory.

## 0.1.2

* FileType: Catch `MappingException` on `getDescription()`.

## 0.1.1

* Use `sys_get_temp_dir()` and `tempnam()` to get a temporary file path if the
  filesystem is local.
* Allow temporary URL generation for `FileInterface`
## 0.1.0

* Initial Release
