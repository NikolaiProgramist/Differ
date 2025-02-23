# Differ

[![tests-check](https://github.com/NikolaiProgramist/php-project-48/actions/workflows/tests-check.yml/badge.svg)](https://github.com/NikolaiProgramist/php-project-48/actions/workflows/tests-check.yml) [![Maintainability](https://api.codeclimate.com/v1/badges/b070d4d02aad3ce48e32/maintainability)](https://codeclimate.com/github/NikolaiProgramist/php-project-48/maintainability) [![Test Coverage](https://api.codeclimate.com/v1/badges/b070d4d02aad3ce48e32/test_coverage)](https://codeclimate.com/github/NikolaiProgramist/php-project-48/test_coverage)

## About

This project is a console utility that finds the difference between two files.
This utility can work with such data formats as: **JSON**, **YAML**, **YML**.
You can also see the comparison result in different output formats, for example: **stylish**, **plain**, **json**.
The default output format is **stylish**.
If the files do not exist, the utility will notify you about it.

## Prerequisites

+ Linux, MacOS, WSL
+ PHP >=8.3
+ Composer
+ Make
+ Git

## Install

```bash
git clone https://github.com/NikolaiProgramist/php-project-48.git
cd php-project-48
make install
```

## Run

```bash
php bin/gendiff --format=stylish file1.json file2.json
```

## Examples in asciinema:

[![asciicast](https://asciinema.org/a/703225.svg)](https://asciinema.org/a/703225)

## Stargazers over time

[![Stargazers over time](https://starchart.cc/NikolaiProgramist/php-project-48.svg?variant=adaptive)](https://starchart.cc/NikolaiProgramist/php-project-48)
