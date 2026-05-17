# LANGMAKER

[![GitHub License](https://img.shields.io/github/license/oc-plus-plus/langmaker?color=green)](https://github.com/oc-plus-plus/langmaker/blob/main/LICENSE)
[![coding standards](https://github.com/oc-plus-plus/langmaker/actions/workflows/coding-standards.yml/badge.svg)](https://github.com/oc-plus-plus/langmaker/actions/workflows/coding-standards.yml)

Automated creation of language modules (`language.ocmod.zip`) for OpenCart 4.x.  
The generated modules install flawlessly and translate everything,
including any other extensions you wish to localize.

The tool automatically and correctly generates the entire directory structure,
controller, extension language files, and the module's admin view template. It pre-configures all settings,
file names, and class names, while copying existing language strings to their appropriate locations.


## Usage

Run the following command from the command line:
```bash
make
```
This will display detailed help and information for all available commands.


## Documentation
The documentation is currently a work in progress and will be available soon.  
We apologize for any temporary inconvenience.


## Contributing

Contributions are welcome! Please read [Contributing][contributing] for details.

[![YAGNI](https://img.shields.io/badge/principle-YAGNI-blueviolet.svg)][yagni]
[![KISS](https://img.shields.io/badge/principle-KISS-blueviolet.svg)][kiss]

In our development, we follow the principles of [YAGNI][yagni] and [KISS][kiss].
The source code should not have extra unnecessary functionality and should be as simple and efficient as possible.


## License

This package is licensed for use under the MIT License (MIT).  
Please see [LICENSE][license] for more information.

[yagni]: https://en.wikipedia.org/wiki/YAGNI
[kiss]: https://en.wikipedia.org/wiki/KISS_principle
[contributing]: https://github.com/oc-plus-plus/langmaker/blob/main/.github/CONTRIBUTING.md
[license]: https://github.com/oc-plus-plus/langmaker/blob/main/LICENSE
