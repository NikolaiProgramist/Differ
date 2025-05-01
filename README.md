# Differ

[![tests-check](https://github.com/NikolaiProgramist/php-project-48/actions/workflows/tests-check.yml/badge.svg)](https://github.com/NikolaiProgramist/php-project-48/actions/workflows/tests-check.yml) [![Maintainability](https://api.codeclimate.com/v1/badges/b070d4d02aad3ce48e32/maintainability)](https://codeclimate.com/github/NikolaiProgramist/php-project-48/maintainability) [![Test Coverage](https://api.codeclimate.com/v1/badges/b070d4d02aad3ce48e32/test_coverage)](https://codeclimate.com/github/NikolaiProgramist/php-project-48/test_coverage)

## About

This project is a library/utility that finds the difference between two files.
The library can work with data formats such as: `JSON`, `YAML`, `YML`.
You can also see the result of the comparison in the terminal in different output formats,
for example: `stylish`, `plain`, `json`. The default format is `stylish`.
If the files do not exist, the library will inform you.

## Prerequisites

+ Linux, MacOS, WSL
+ PHP >=8.3
+ Composer
+ Make
+ Git

## Libraries

+ php-cli-tools
+ docopt
+ functional-php
+ yaml

## Install project

Downloading the utility and installing dependencies:

```bash
git clone https://github.com/NikolaiProgramist/Differ.git
cd php-project-48
make install
```

Give the binary file execution rights:

```bash
sudo chmod +x bin/gendiff
```

## Run

To use the utility, run the binary file and specify the output format (`stylish` by default).
Also pass the paths to the two files you need:

```bash
bin/gendiff --format=stylish file1.json file2.json
```

You can also choose the second output format `plain`:

```bash
bin/gendiff --format=plain file1.json file2.json
```

Instead of the flag `--format`, you can use the short version `-f`.

## Examples:

[![asciicast](https://asciinema.org/a/703225.svg)](https://asciinema.org/a/703225)

## Stargazers over time

[![Stargazers over time](https://starchart.cc/NikolaiProgramist/php-project-48.svg?variant=adaptive)](https://starchart.cc/NikolaiProgramist/php-project-48)
