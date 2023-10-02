# CHANGELOG

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
