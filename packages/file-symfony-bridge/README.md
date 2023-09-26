# rekalogika/file-symfony-bridge

Provides integrations for Rekalogika FileInterface with Symfony HttpFoundation,
Form, and Validator.

## Features

* Adapters to convert HttpFoundation `File` objects to a `FileInterface` and
  vice versa, with special handling for `UploadedFile`.
* `FileResponse` for streaming a `FileInterface` to the client web browser.
* `FileType` form that works with `FileInterface` objects.
* A form transformer `FileTransformer` that you can add to an existing Symfony
  `FileType` fields so that it gives us a `FileInterface` instead of a
  `UploadedFile` object.
* A form extension `FileTypeExtension` that you can optionally register to
  automatically convert all the existing Symfony `FileType` so they all give us
  a `FileInterface`.
* Subclassed `FileValidator` and `ImageValidator` that works with
  `FileInterface` objects.

## Documentation

[rekalogika.dev/file-bundle](https://rekalogika.dev/file-bundle)

## License

MIT

## Contributing

The `rekalogika/file-symfony-bridge` repository is a read-only repo split from
the main repo. Issues and pull requests should be submitted to the
[rekalogika/file-src](https://github.com/rekalogika/file-src) monorepo.
