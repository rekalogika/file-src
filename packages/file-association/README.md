# rekalogika/file-association

Handles the association between Doctrine entities and files using the
rekalogika/file framework, including from file uploads.

## Features

* Requires only a single property in the entity for each associated file.
* File properties are file properties. It is not necessary to store any of the
  file's properties in the entity associated with the file.
* DX improvement, less micro-management of entity-file relations.
* Reads and writes directly into the file properties, even if private. You are
  free to have business logic in the getters and setters.
* Doesn't require you to update another property of the entity (`lastUpdated`?)
  just to make sure the correct Doctrine events will be fired.

## Documentation

[rekalogika.dev/file-bundle](https://rekalogika.dev/file-bundle)

## License

MIT

## Contributing

The `rekalogika/file-association` repository is a read-only repo split from the
main repo. Issues and pull requests should be submitted to the
[rekalogika/file-src](https://github.com/rekalogika/file-src) monorepo.
