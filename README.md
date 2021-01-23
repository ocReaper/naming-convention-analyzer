# ocReaper naming convention analyzer

[![Latest version](https://img.shields.io/packagist/v/ocreaper/naming-convention-analyzer.svg?colorB=007EC6)](https://packagist.org/packages/ocreaper/naming-convention-analyzer)
[![Downloads](https://img.shields.io/packagist/dt/ocreaper/naming-convention-analyzer.svg?colorB=007EC6)](https://packagist.org/packages/ocreaper/naming-convention-analyzer)

ocReaper naming convention analyzer for [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) provides sniffs that ensure proper variable and function naming.

The ensured convention is identical to [kettanaito/naming-cheatsheet](https://github.com/kettanaito/naming-cheatsheet).

The project uses [tbrianjones/Inflect.php](https://gist.github.com/tbrianjones/ba0460cc1d55f357e00b) to handle pluralization.

## Table of contents

1. [Sniffs included in this standard](#sniffs-included-in-this-standard)
2. [Installation](#installation)
3. [How to run the sniffs](#how-to-run-the-sniffs)
  - [Choose which sniffs to run](#choose-which-sniffs-to-run)
  - [Using all sniffs from the standard](#using-all-sniffs-from-the-standard)
4. [Fixing errors automatically](#fixing-errors-automatically)
5. [Suppressing sniffs locally](#suppressing-sniffs-locally)
6. [Contributing](#contributing)

## Sniffs included in this standard

🔧 = [Automatic errors fixing](#fixing-errors-automatically)

#### NamingConventionAnalyzer.Functions.ActionPrefix

Ensures that a function must start with a verb. In case the function starts with a prefix: the second word will be used in check.

#### NamingConventionAnalyzer.Functions.BoolPrefix

Ensures that functions that returns boolean value must start with:

- has
- is
- should

#### NamingConventionAnalyzer.Functions.ContextDuplication

Ensures that a function does not duplicate the name of the class it belongs to.

#### NamingConventionAnalyzer.Variables.ContextDuplication

Ensures that a variable does not duplicate the name of the function it belongs to.

#### NamingConventionAnalyzer.Variables.SingularPlural

Ensures that the name of those variables that are containing non-associative arrays are in plural.

#### Squiz.NamingConventions.ValidFunctionName.NotCamelCaps

Ensures that function names are in camel case.

#### Squiz.NamingConventions.ValidVariableName.NotCamelCaps

Ensures that variable names are in camel case.

#### Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

Ensures that class member names are in camel case.

#### Squiz.NamingConventions.ValidVariableName.StringNotCamelCaps

Ensures that variable names inside a doublequoted string are in camel case.

## Installation

The recommended way to install ocReaper naming convention analyzer is [through Composer](http://getcomposer.org).

```JSON
{
    "require-dev": {
        "ocreaper/naming-convention-analyzer": "0.4.1"
    }
}
```
## How to run the sniffs

You can choose one of two ways to run only selected sniffs from the standard on your codebase:

### Choose which sniffs to run

The recommended way is to write your own ruleset.xml by referencing only the selected sniffs. This is a sample ruleset.xml:

```xml
<?xml version="1.0"?>
<ruleset name="AcmeProject">
	<config name="installed_paths" value="../../ocreaper/naming-convention-analyzer"/><!-- relative path from PHPCS source location -->
	<rule ref="NamingConventionAnalyzer.Functions.ContextDuplication"/>
	<!-- other sniffs to include -->
</ruleset>
```

Then run the `phpcs` executable the usual way:

```
vendor/bin/phpcs --standard=ruleset.xml --extensions=php --tab-width=4 -sp src tests
```

### Exclude sniffs you don't want to run

You can also mention ocReaper naming convention analyzer in your project's `ruleset.xml` and exclude only some sniffs:

```xml
<?xml version="1.0"?>
<ruleset name="AcmeProject">
	<rule ref="vendor/ocreaper/naming-convention-analyzer/NamingConventionAnalyzer/ruleset.xml"><!-- relative path to your ruleset.xml -->
		<!-- sniffs to exclude -->
	</rule>
</ruleset>
```

However it is not a recommended way to use ocReaper naming convention analyzer, because your build can break when moving between minor versions of the standard (which can happen if you use `^` or `~` version constraint in `composer.json`). I regularly add new sniffs even in minor versions meaning your code won't most likely comply with new minor versions of the package.

## Fixing errors automatically

Sniffs in this standard marked by the 🔧 symbol support [automatic fixing of coding standard violations](https://github.com/squizlabs/PHP_CodeSniffer/wiki/Fixing-Errors-Automatically). To fix your code automatically, run phpcbf instead of phpcs:

```
vendor/bin/phpcbf --standard=ruleset.xml --extensions=php --tab-width=4 -sp src tests
```

Always remember to back up your code before performing automatic fixes and check the results with your own eyes as the automatic fixer can sometimes produce unwanted results.

## Contributing

To make this repository work on your machine, clone it and run these two commands in the root directory of the repository:

```
composer install
```
