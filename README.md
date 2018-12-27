Source code of the tutorial [thecodingmachine.io/building-a-single-page-application-with-symfony-4-and-vuejs](https://thecodingmachine.io/building-a-single-page-application-with-symfony-4-and-vuejs).

# Quick start

If you want to try out the project just follow these steps.

### Start containers / project
`docker-compose up`

### Enter in your app container
`docker-compose exec app /bin/bash`

### Install vendor
`composer install`

### Install node modules
`npm install`

### Generate manifest
`yarn dev`

### Generate database
`php bin/console doctrine:migration:migrate`

### Load fixtures
`php bin/console doctrine:fixtures:load`

### Access
App: app.localhost (login: foo / pass: bar)

phpMyAdmin: phpadmin.app.localhost

