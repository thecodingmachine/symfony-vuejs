**A new version with Symfony + GraphQL + Nuxt.js is available here:** https://github.com/thecodingmachine/symfony-boilerplate

---

Source code of the tutorial [thecodingmachine.io/building-a-single-page-application-with-symfony-4-and-vuejs](https://thecodingmachine.io/building-a-single-page-application-with-symfony-4-and-vuejs).

# Quick start

If you want to try out the project just follow those steps:

```bash
$ cp .env.template .env
$ docker-compose up -d
$ docker-compose exec app bash # executing bash inside app service
$ composer install
$ yarn install
$ yarn dev
$ php bin/console doctrine:migration:migrate
$ php bin/console doctrine:fixtures:load
```

On MacOS, also update your `/etc/hosts` file with:

```
127.0.0.1   app.localhost
127.0.0.1   phpmyadmin.app.localhost
```

You may now go to [http://app.localhost/](http://app.localhost/) and
login using the following credentials:

Login: `foo`
Password: `bar`
