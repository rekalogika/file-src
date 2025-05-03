# CHANGELOG

## 2.0.0

* chore: clean up `FileAssociationManager`
* refactor: Make `FileAssociationManager` use `ClassBasedFileLocationResolverInterface`
* chore: remove deprecated `FileLocationResolverInterface`
* refactor: move property related methods to `FilePropertyManagerInterface`
* refactor: use `PropertyMetadata` as arguments for simplicity
* refactor: make `PropertyListerInterface` return property name and the class
  scope
* test: enable monologbundle

## 1.14.0

* fix: fix web serving
* refactor(PropertyInspector): change interface to accept class, not object;
  BC break but probably won't affect anyone. add caching using symfony/cache.
* chore: rector run
* fix: fix interface phpdoc
* fix: remove no longer needed psr-16
* refactor: introduce `ClassMetadataFactoryInterface` to replace
  `PropertyInspectorInterface`
* refactor: use `FetchMode` enum instead of EAGER and LAZY strings
* refactor: internalize property lister, property reader, property writer
* feat: `ClassSignatureResolverInterface` for overriding class signatures used
  in file locations
* refactor: make `FileAssociationManager` use `ClassMetadataFactoryInterface`
  instead of `PropertyListerInterface`
* deprecation: `FileLocationResolverInterface`

## 1.13.0

* deps: remove dependency on `doctrine/persistence`
* fix: correctly handle common proxy objects
* deps: update JS dependencies

## 1.12.0

* fix: fix phpstan errors
* build: schedule weekly CI
* build: update to phpstan 2
* build: update to psalm 6
* fix: handle attempts to resize corrupt images
* feat: `FileLocationResolverCommand` to debug file location

## 1.11.0

* test: modernization
* fix(filepond): clear field class, fix issues with Bootstrap 4
* fix(filepond): also clear style attribute just in case

## 1.10.2

* chore: cleanup `composer.json`
* deps: avoid buggy reconstitutor versions

## 1.10.1

* deps: update javascript dependencies

## 1.10.0

* deps: drop PHP 8.1 support

## 1.9.0

* feat: PHP 8.4 compatibility

## 1.8.10

* chore: add and set rector

## 1.8.9

* fix: creates `DateTime` objects with the default timezone.

## 1.8.8

* fix: wrong type hint for `ContainerInterface`

## 1.8.7

* fix: remove dependency on `ServiceSubscriberTrait`, fixes deprecated warning
  from `symfony/service-contracts`

## 1.8.6

* feat: AssetMapper compatibility
* deps: Update oneup/uploaderbundle requirement from 4.0 to 5.0

## 1.8.4

* fix: inconsistent intervention version
* fix: `MetadataFactory` return type hint

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
