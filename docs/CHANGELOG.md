# Change Log

All notable changes to this project will be documented in this file.

This project adheres to [Semantic Versioning](http://semver.org/).
Given a version number MAJOR.MINOR.PATCH, increment the:

    MAJOR version when you make incompatible API changes,
    MINOR version when you add functionality in a backwards-compatible manner, and
    PATCH version when you make backwards-compatible bug fixes.

### Version 0.2.0
- Add class `Validate` that depends on library `eclipxe/buzoncfdi-cfdireader`.
  It makes two types of validation, one against XSD files and the other against rules.
- Change: make the method XmlResolver::newRetriever public
- Add test `FullInvoiceCaseTest` to create an invoice with all valid data and validations.
- Add validation example to the README.md file 

### Version 0.1.0
- Initial public release.
- IMPORTANT: This project is a fork with no compatibility with upstream library orlandocharles/cfdi
- The license is the same (MIT) but copyright changes due deep changes and new code

Copyright for portions of this project are held by Orlando Charles <me@orlandocharles.com>, 2017
as part of project orlandocharles/cfdi (https://github.com/orlandocharles/cfdi) MIT License.
All other copyright for this project are held by Carlos C Soto, 2017.
