## `.env` contents

`.env` contents for the GitLab CI/CD jobs.

### API

`SYMFONY_ENV_CONTENT_TESTS`

```
APP_ENV=foo
APP_DEBUG=0
APP_SECRET=A_SECRET
COOKIE_DOMAIN=.foo.bar
CORS_ALLOW_ORIGIN=http://foo.bar
MONOLOG_LOGGING_PATH=php://stderr
DATABASE_URL=mysql://null:null@mysql:3306/null?server_version=8.0
TESTS_DATABASE_URL=mysql://foo:foo@mysql:3306/foo?server_version=8.0
MESSENGER_TRANSPORT_DSN=redis://null@null:6379/messages
STORAGE_PUBLIC_SOURCE=foo
STORAGE_PRIVATE_SOURCE=foo
STORAGE_ENDPOINT=foo
STORAGE_PUBLIC_BUCKET=foo
STORAGE_PRIVATE_BUCKET=foo
STORAGE_ACCESS_KEY=foo
STORAGE_SECRET_KEY=foo
DEFAULT_LOCALE=en
MAILER_DSN=smtp://null:null@null:1025
MAIL_FROM_ADDRESS=no-reply@foo.bar
MAIL_FROM_NAME=foo
MAIL_WEBAPP_URL=http://foo.bar
MAIL_WEBAPP_UPDATE_PASSWORD_ROUTE_FORMAT=%s/update-password/%s/%s
```

**Note:** we are using dummy values for some environment variables. Sometimes because they are useless for tests
or because we define them in the PHPUnit configuration file [src/api/phpunit.xml.dist](../../src/api/phpunit.xml.dist).

#### Testing, staging and production

### Web application