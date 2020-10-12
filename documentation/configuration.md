# Configuration

This documentation will explain how to configure your development environment, the web application, and the API.

## Development environment

Your development environment mostly consists of two files:

* [docker-compose.yml](../docker-compose.yml)
* `.env` file (and its template [.env.dist](../.env.dist))

Docker Compose can read the variables (and their values) from the `.env` file.

For instance:

```
FOO=hello
```

```yaml
service_foo:
    environment:
      FOO: "$FOO"
```

Becomes at runtime (e.g., when running a Docker Compose command):

```yaml
service_foo:
    environment:
      FOO: "hello"
```

It would be best to put all variables that are either secrets or shared across services in the `.env` file.

When adding a new variable in the `.env` file, don't forget to update the template [.env.dist](../.env.dist) with it.
It will help other developers to notice this change and update their own `.env` files accordingly.

You should never commit the `.env` file as it may contain secrets.

**Always use dummy values for your secrets in the [.env.dist](../.env.dist) file.**

## Vagrant

Vagrant's configuration consists of three files:

* [Makefile](../Makefile)
* [scripts/create-vagrantfile-from-template.sh](../scripts/create-vagrantfile-from-template.sh)
* `Vagrantfile` (and its template [Vagrantfile.template](../Vagrantfile.template))

The [Makefile](../Makefile) contains variables like `VAGRANT_BOX`. The command `make vagrant` launches 
the script [create-vagrantfile-from-template.sh](../scripts/create-vagrantfile-from-template.sh), which reads
these variables and replaces placeholders from the [Vagrantfile.template](../Vagrantfile.template) by their values into 
a new `Vagrantfile`.

You should never commit the `Vagrantfile`.

## Extend a Docker image

You might need to extend a Docker image for installing one or more packages.

For instance, let's say you want to install the `pdftk` package for the API:

```Dockerfile
# src/api/Dockerfile
FROM thecodingmachine/php:7.4-v3-apache AS extended

# Always use the root user for installing packages.
USER root

# Install PDFtk.
RUN DEBIAN_FRONTEND=noninteractive apt-get install -y -qq --no-install-recommends pdftk &&\
    # Print versions of PDFtk.
    pdftk --version

# Go back to the default Docker image user.
USER docker

FROM extended
# Your production Docker image instructions.
```

In your [docker-compose.yml](../docker-compose.yml) file:

```yaml
api:
  #image: thecodingmachine/php:7.4-v3-apache
  build:
    context: "./src/api"
    target: "extended"
```

Finally, update your [Makefile](../Makefile):

```makefile
# Start the Docker Compose stack.
up:
    docker-compose up --build -d
```

# API

The [src/api/config](../src/api/config) folder contains the configuration of Symfony.

There are two main parts:

* [src/api/config/services.yaml](../src/api/config/services.yaml): YAML configuration file for your application.
* [src/api/config/packages](../src/api/config/packages) folder: YAML configuration files of the bundles (Symfony packages).

The [packages](../src/api/config/packages) folder root files are the default configurations of the bundles.

According to the `APP_ENV` value of the `api` service, files from [src/api/config/packages/dev](../src/api/config/packages/dev), 
[src/api/config/packages/test](../src/api/config/packages/test), 
or [src/api/config/packages/prod](../src/api/config/packages/prod) folders will extend the default configurations.

You often don't want to extend a configuration directly but instead create a variable. For instance,
the `DATABASE_URL` value contains secrets (the database hostname, password, etc.) you should not commit.
Also, you might use `APP_ENV=prod` for different environments (like staging and production), which do not use the same 
database.

That's why Symfony allows doing the following:

```yaml
doctrine:
    dbal:
        url: '%env(resolve:DATABASE_URL)%'
```

This instruction will fetch the value of the given environment variable. 
See the [official documentation](https://symfony.com/doc/current/configuration/env_var_processors.html) for more details.

**In your development environment, do not put these environment variables in the `.env` file 
from the [src/api](../src/api) folder, but instead, put them under the `environment` key 
from the `api` service of your [docker-compose.yml](../docker-compose.yml) file.**

If you need the value of an environment variable in your code, use the Symfony parameters 
(see [src/api/config/services.yaml](../src/api/config/services.yaml)).

For instance:

```yaml
# src/api/config/services.yaml.
parameters:
    app.foo: : '%env(FOO)%'
```

```php
# A class.
private string $foo;

public function __construct(
    ParameterBagInterface $parameters
) {
    $this->foo = $parameters->get('app.foo');
}
```

# Web application

The [src/webapp/nuxt.config.js](../src/webapp/nuxt.config.js) file contains the configuration of Nuxt.js.

You may use environment variables in this file. They are available through 
the instruction `process.env.YOUR_ENVIRONMENT_VARIABLE_NAME`.

**Put them under the `environment` key from the `webapp` service of your [docker-compose.yml](../docker-compose.yml) 
file.**

If you need the value of an environment variable in your code, put it under the `publicRuntimeConfig` or 
`privateRuntimeConfig` section of the [nuxt.config.js](../src/webapp/nuxt.config.js) file  in your development environment.

For instance:

```js
publicRuntimeConfig: {
    apiURL: process.env.API_URL,
}
```

The value is available in your code thanks to `$config.apiURL` (in your `template` blocks) 
or `this.$config.apiURL` (in your Vue components).

**Note:** `privateRuntimeConfig` should contain your secrets. Values from this section **are only available when 
Nuxt.js executes your code on the server.**

---

[Back to top](#configuration) - [Home](../README.md)