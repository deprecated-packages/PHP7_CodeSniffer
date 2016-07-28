# CodeSniffer in PHP 7, with simple usage everyone understands

This is essential development tool that ensures your code **remains clean and consistent**.

[![Build Status](https://img.shields.io/travis/Symplify/PHP7_CodeSniffer.svg?style=flat-square)](https://travis-ci.org/Symplify/PHP7_CodeSniffer)
[![Quality Score](https://img.shields.io/scrutinizer/g/Symplify/PHP7_CodeSniffer.svg?style=flat-square)](https://scrutinizer-ci.com/g/Symplify/PHP7_CodeSniffer)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/Symplify/PHP7_CodeSniffer.svg?style=flat-square)](https://scrutinizer-ci.com/g/Symplify/PHP7_CodeSniffer)
[![Downloads total](https://img.shields.io/packagist/dt/symplify/php7_codesniffer.svg?style=flat-square)](https://packagist.org/packages/symplify/php7_codesniffer)
[![Latest stable](https://img.shields.io/packagist/v/symplify/php7_codesniffer.svg?style=flat-square)](https://packagist.org/packages/symplify/php7_codesniffer)


## Install

Via composer:

```json
composer require symplify/php7_codesniffer --dev
```

## Usage

Run it from cli:

```bash
./vendor/bin/php7cs src
```

To fix the issues just add `--fix`:

```bash
./vendor/bin/php7cs src --fix
```

### How to Use Specific Standard?

```bash
./vendor/bin/php7cs src --standards=PSR2
./vendor/bin/php7cs src --standards=PSR2,Zend
```

### How to Use Specific Sniff?

```bash
./vendor/bin/php7cs src --sniffs=PSR2.Classes.ClassDeclaration
./vendor/bin/php7cs src --sniffs=PSR2.Classes.ClassDeclaration,Zend.Files.ClosingTag
```

You can combine them as well:

```bash
./vendor/bin/php7cs src --standards=PSR2 --sniffs=Zend.Files.ClosingTag
```


## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md) for information.
