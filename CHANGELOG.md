# CHANGELOG

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
