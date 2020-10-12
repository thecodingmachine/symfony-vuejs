# Housekeeping

* [Back to README](README.md)

As the team (tech) leader, this documentation will help you to keep everything up-to-date. 
Please read it carefully, as those actions will ensure your project is still relevant year after year. 
The sooner the better!

## Docker and Docker compose

* [Docker and Docker Compose releases](https://docs.docker.com/release-notes/)

Make sure you and your team always use the latest versions of Docker and Docker Compose.

For non-Vagrant users, follow the [Docker official documentation](https://docs.docker.com/engine/install/#server).
For Docker Compose, also follow the [official documentation](https://docs.docker.com/compose/install/#install-compose-on-linux-systems).

For Vagrant users, as the team (tech) leader, you should update the variable `DOCKER_COMPOSE_VERSION` of the [Makefile](Makefile).
Then, Vagrant users will have to run:

```
# If the VM is running.
vagrant halt

# Always.
vagrant destroy
make vagrant
vagrant up

# Versions check.
vagrant ssh
docker --version
docker-compose --version
```

It will re-create the Vagrant VM with the latest version of Docker and Docker Compose.

**Note:** from time to time, you may also update the `VAGRANT_BOX` variable from the [Makefile](Makefile)
with a newer [Ubuntu box](https://app.vagrantup.com/bento). The update process for Vagrant users is the same as
described before.

## Vagrant and VirtualBox

Run `vagrant version` to see your current version and the latest one. 
Follow the printed instructions for upgrading Vagrant if required.

For VirtualBox, simply open the application, it should tell you to download the newer version, if any.

## Docker Compose files

As you know, Docker Compose files describe many services. Each service uses an image and a version.

By default, most of the versions have to use X.Y format (X for major updates, Y for minor ones).

The idea here is that running `docker-compose pull` will only update images with bugs fixes (for most images, see below).

As the team (tech) leader, you should define a day of the week when all the team members have to run `docker-compose pull` 
on their development environment. For remote environments, it should be done on each deployment (preferably automatically).

> Update the following list according to your services.

### Traefik

* [Traefik releases](https://github.com/containous/traefik/releases)

**Bugs fixes:** run `docker-compose pull`.

**Minor version:** you should be able to change the minor version of your image without any problem, as it should only
contain new features and bugs fixes. Anyway, read carefully the patch note and the related documentation. Update the
corresponding Docker Compose files accordingly.

**Major version:** as a major version contains breaking changes, read carefully the patch note and the new documentation.
Update the corresponding Docker Compose files accordingly.

### TheCodingMachine images (Node.js / PHP)

* [TheCodingMachine NodeJS releases](https://github.com/thecodingmachine/docker-images-nodejs#images)
* [TheCodingMachine PHP releases](https://github.com/thecodingmachine/docker-images-php#images)

**Bugs fixes:** run `docker-compose pull`.

**Minor/major versions:** actually the version of the underlying technology (Node.js / PHP). As soon as
a new version is available (LTS for Node.js, anything for PHP), you should update the corresponding Docker Compose files 
plus your source code if necessary. For PHP, some tools like [PHPStan](https://github.com/phpstan/phpstan),
[PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) or [Rector](https://github.com/rectorphp/rector) might 
help you along the way.

// TODO Node.js?

### MySQL

* [MySQL releases](https://hub.docker.com/_/mysql?tab=tags)

**Bugs fixes:** run `docker-compose pull`.

**Minor version:** you should be able to change the minor version of your image without any problem, as it should only
contain new features and bugs fixes. Anyway, read carefully the patch note and the related documentation. Update the
corresponding Docker Compose files accordingly.

**Major version:** as a major version contains breaking changes, read carefully the patch note and the new documentation.
Update the corresponding Docker Compose files accordingly. You might have to wait for a [TDBM](https://github.com/thecodingmachine/tdbm)
new release (do not hesitate to post an issue if you seek for information).

### phpMyAdmin

* [phpMyAdmin releases](https://github.com/phpmyadmin/phpmyadmin/releases)

**Bugs fixes:** run `docker-compose pull`.

**Minor/major versions:** as for minor versions, major versions should be painless to update. Anyway, read carefully the 
patch note and the related documentation. Update the corresponding Docker Compose files accordingly.

### Redis

* [Redis releases](https://hub.docker.com/r/bitnami/redis/tags)

**Bugs fixes:** run `docker-compose pull`.

**Minor version:** you should be able to change the minor version of your image without any problem, as it should only
contain new features and bugs fixes. Anyway, read carefully the patch note and the related documentation. Update the
corresponding Docker Compose files accordingly.

**Major version:** as a major version contains breaking changes, read carefully the patch note and the new documentation.
Update the corresponding Docker Compose files accordingly. You might also have to update the Symfony messenger configuration.

### MailHog

* [MailHog releases](https://github.com/mailhog/MailHog/releases)

**Bugs fixes:** run `docker-compose pull`.

**Minor/major versions:** run `docker-compose pull`. As for minor versions, major versions should be painless to update. 
Anyway, read carefully the patch note and the related documentation. Update the corresponding Docker Compose files accordingly.

### MinIO

* [MinIO releases](https://github.com/minio/minio/releases)

**Bugs fixes:** run `docker-compose pull`.

**Minor/major versions:** run `docker-compose pull`. As for minor versions, major versions should be painless to update. 
Anyway, read carefully the patch note and the related documentation. Update the corresponding Docker Compose files accordingly.

## Webapp

From time to time, check for new releases of your main packages: 

1. Update the corresponding versions in your `package.json` file.
2. Remove the file `yarn.lock` and the folder `node_modules`.
3. Recreate the `webapp` service with `docker-compose up -d --force webapp`.

## API

### Dependencies

**Almost every day**, you should run `composer update` (or `composer update --prefer-source` for Vagrant users) 
in order to keep your PHP dependencies up-to-date. Also run `composer update --lock` to suppress warning about the 
lock file being out of date.

To know which packages are outdated, run `composer outdated --direct` and update your `composer.json` file accordingly.
Read carefully the patch note and the related documentation before updating your code.

### Symfony

* [Symfony releases](https://symfony.com/releases)

**Minor versions:** https://symfony.com/doc/current/setup/upgrade_minor.html.

**Major versions:** https://symfony.com/doc/current/setup/upgrade_major.html.

As a team (tech) leader, you should update to the latest minor/majors version whenever a new version is available*.

*\* For major versions, make sure your Symfony bundles are ready before updating.*






