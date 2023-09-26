# rekalogika/file

High-level file abstraction library built on top of Flysystem. It lets you work
with file objects in an object-oriented manner. A file object represents a file
in a Flysystem filesystem. It can be a local file or a file in a cloud storage,
the library lets you work with them in the same way.

## Features

* Rich, high-level abstraction of files built on top of Flysystem.
* Abstractions for file name and media type (MIME type).
* Caches and stores metadata in a sidecar file. Uniform metadata support across
  all filesystems.
* Uses the repository pattern for files.
* Remote façade pattern in accessing metadata. Improves performance with remote
  filesystems. Two metadata queries require only one round trip.
* Rich metadata support.
* Option to use lazy-loading proxy for files.
* Support for file derivations.
* Separated contracts and implementation. Useful for enforcing architectural
  boundaries. Your domain models don't have to depend on the framework.

## Documentation

[rekalogika.dev/file](https://rekalogika.dev/file)

## License

MIT

## Contributing

The `rekalogika/file` repository is a read-only repo split from the main repo.
Issues and pull requests should be submitted to the
[rekalogika/file-src](https://github.com/rekalogika/file-src) monorepo.
