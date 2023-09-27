# CHANGELOG

## 0.1.3

* `AttributesPropertyLister` now caches the results in memory.

## 0.1.2

* FileType: Catch `MappingException` on `getDescription()`.

## 0.1.1

* Use `sys_get_temp_dir()` and `tempnam()` to get a temporary file path if the
  filesystem is local.
* Allow temporary URL generation for `FileInterface`
## 0.1.0

* Initial Release
