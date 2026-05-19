# LangMaker

[![GitHub License](https://img.shields.io/github/license/oc-plus-plus/langmaker?color=green)](https://github.com/oc-plus-plus/langmaker/blob/main/LICENSE)
[![coding standards](https://github.com/oc-plus-plus/langmaker/actions/workflows/coding-standards.yml/badge.svg)](https://github.com/oc-plus-plus/langmaker/actions/workflows/coding-standards.yml)

## Automated creation of language modules

The tool allows you to automate creation of language modules (`language.ocmod.zip`) for OpenCart 4.x.  
The generated modules install flawlessly and translate everything,
including any other extensions you wish to localize.

The tool automatically and correctly generates the entire directory structure,
controller, extension language files, and the module's admin view template. It pre-configures all settings,
file names, and class names, while copying existing language strings to their appropriate locations.

## Language Comparison & Synchronization

The tool allows you to compare a base (source) language with a target language selected for verification. During this process, the target language's directory structure and its `key => value` array contents will be completely rebuilt to match the base language exactly.

- **Missing files** are automatically copied from the base language to the target language.
- **Redundant files** found in the target language are moved to a `.redundant` folder.
- **Line-by-line comparison** of keys is performed between the base and target languages:
	- **Missing keys** will be added to the target file and marked with a `/* LM ADDED */` comment.
	- **Redundant keys** will be commented out and moved to the end of the file.
	- **Existing keys** are preserved along with their current values (translations).
	- **Comments and other PHP code** are synchronized from the base language "as is".

## Requirements

To use this tool, you must have PHP 8.1 or newer available via the command line (CLI), with the `zip` extension enabled. While `make` is recommended, it is optional; you can still use the tool without it by directly invoking the required PHP scripts.

## Usage

Run the following command from the command line:
- Display detailed help and information for available commands.
	```bash
	make
	```
- Creating installable OpenCart 4.x modules for languages.
  - There is an optional `lng` parameter available. When specified, the module will be built exclusively for the
  designated language (among those available).
	```bash
	make module lng=french
	```
  - If invoked without this parameter, modules will be generated for all available languages.
	```bash
	make module
	```
- Language Comparison & Synchronization. This command accepts 2 parameters:
  - `master` — (Optional) Specifies the base language to be used as a reference when comparing with other languages.
  Defaults to `"english"` if omitted, but can be set to any of the available languages.
  - `compare` — Specifies the target language to be verified and compared against the base language.
	```bash
	make rewrite check=french
	```
 	or
	```bash
	make rewrite master=english check=french
	```


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
