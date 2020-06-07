**Caution:** this is still work in progress.

---

# API

A PHP backend based on Symfony and with GraphQL endpoints.

**All commands have to be run in the `api` service (`make api`).**

## Configuration

## Code

### Conventions

#### Comments

Comments should only be added if a piece of code is not clear enough.
**Most of the time, using explicit class/function/exception names should be enough.**

Anyway, comments have to start with an uppercase and end with a punctuation.

For instance:

```php
// this code do X because of Y <- incorrect
// Do X because of Y. <- correct
``` 

### Creating a use case

### Tests

// TODO explain how to write tests.
// TODO explain that a use case must be injected somewhere before being usable in tests.

Before pushing your commits to the repository or even while coding, run the following command:

```
composer pest
```

It will execute all tests and display the result and the code coverage in your terminal.

Speaking of code coverage, an HTML output is also available under the `converage` folder.
Do not hesitate to take a look at it!

From time to time, also check the [Pest release page](https://github.com/pestphp/pest/releases)
and [PHP Unit release page](https://github.com/sebastianbergmann/phpunit/releases).
If new releases are available, run `composer update`.
If a new major (**X**.Y.Z) or minor (X.**Y**.Z) release is available, update your `composer.json` file first.

### Static analysis tools

Before pushing your commits to the repository or even while coding, run the following commands:

```
composer csfix &&
composer cscheck &&
composer phpstan &&
composer deptrac &&
composer yaml-lint
```

They will analyze your code and give you feedback on what's wrong.
`composer csfix` will also format your code.

## Composer

When installing a PHP dependency, ask yourself if it is a `dev` dependency or not:

```
composer require [--dev] [package]
```

As we're using Symfony, make sure to choose the package with Symfony support (aka bundle) if available.

**Vagrant user might encounter some issues with Composer. 
A workaround solution is to add the flag `--pref-source` to your Composer command.**

From time to time, don't forget to update the dependencies:

```
composer update
```

## Database

### Structure

Doctrine migrations manage the database structure.
They can be found in the `migrations` folder.

Run the following command to create a new migration:

```
php bin/console doctrine:migrations:generate
```

Add a meaningful description:

```
public function getDescription() : string
{
    return 'Create X, Y and Z tables.';
}
```

Throw an exception in the `down` function:

```
public function down(Schema $schema) : void
{
    throw new RuntimeException('Never rollback a migration!');
}
```

Next you have to apply this migration to the database:

```
php bin/console doctrine:migrations:migrate -n
```

If everything went smooth, regenerate the TDBM models and DAOs with:

```
php bin/console tdbm:generate
```

**You must verify you didn't break anything: use the static analysis tools and tests.**

> Remove the next paragraph when you'll have a distant environment
> on which you can't reset the database.

If you're updating an existing table edit the migration where the table is defined
instead of creating a new migration.

Once done, reset the database, recreate it, apply the migrations and finally
regenerate the TDBM models and DAOs with:

```
php bin/console doctrine:database:drop -n --force &&
php bin/console doctrine:database:create -n &&
php bin/console doctrine:migrations:migrate -n &&
php bin/console tdbm:generate
```

### Data for development

// TODO

## Housekeeping