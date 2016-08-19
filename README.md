# What is the documentor

This documentation generator creates a html documentation for php classes based on the comments provided for classes, interfaces, traits, methods, variables etc. The generated output is html, css and js and can be styled with custom themes as desired.

## Requirements

* PHP Version >= 7.0
* phpOMS

## Usage

A list of arguments can be found with:

```
php Documentor/src/index.php -h
```

The default usage would be:

```
php Documentor/src/index.php -s <SOURCE_PATH> -d <DESTINATION_PATH> -c <COVERAGE_PATH>
```

### Arguments

* `-h` Show help
* `-s` Source directory
* `-d` Destination directory
* `-c` Code coverage source (`coverage-clover`)
* `-u` Unit test log (`junit` style)
* `-g` Html guide source

## Supported Key Words

The following key words hold special meaning in the code documentation.

* @author
* @var
* @param
* @version
* @since
* @latex
* @example
* @output
* @annotation
* @license
* @link
* @category
* @package
* @return
* @throws
* @todo
* @uses
* @see
* @deprecated