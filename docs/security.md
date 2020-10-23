# Security

This documentation will explain how to secure the API and web application.

## API

Security for the API has many scopes:

1. Authentication and users' sessions.
2. Users' permissions.
3. Access control (GraphQL, Symfony routes).
4. CORS.

### Authentication

[GraphQLite](https://graphqlite.thecodingmachine.io/) exposes three GraphQL entry points 
(you do not have to create them manually):

1. The `login` mutation: takes a `userName` and a `password`. It returns a `User` type on success.
2. The `logout` mutation.
3. The `me` query: returns a `User` type if authenticated, null otherwise.

[GraphQLite](https://graphqlite.thecodingmachine.io/) hooks itself to the authentication mechanisms of Symfony,
but it needs some help with that task.

For instance, we defined a [src/api/src/Infrastructure/Security/UserProvider](../src/api/src/Infrastructure/Security/UserProvider.php).
Its goal is to tell which class represents our users and load an instance of this class according to the session's data.

We tell Symfony that we use this custom user provider in the configuration file 
[src/api/config/packages/security.yaml](../src/api/config/packages/security.yaml).

In the application, we defined that class [src/api/src/Domain/Model/User](../src/api/src/Domain/Model/User.php)
represents our users. It implements the `UserInterface` from Symfony.

There are many methods to implement, but the most important ones are:

* `getUsername`: the "username" value (the user's email in our case).
* `getPassword`: the salted / encoded password (TDBM provides the implementation - see [src/api/src/Domain/Model/Generated/BaseUser](../src/api/src/Domain/Model/Generated/BaseUser.php)).
* `getRoles`: the Symfony permissions (e.g. `ROLE_FOO`, `ROLE_BAR`, etc.) - more on that later.

On login, Symfony provides a `PHPSESSID` cookie to the browser. On logout or session expiration, it deletes this cookie.

This cookie is only available on the main domain and its subdomains. For instance, if your API URL is `https://api.foo.com`
and you call the `login` mutation from `https://foo.com`, the cookie will be available. It will not be the case 
from `https://bar.com`.

We store the sessions' data in the MySQL database (`sessions` table). We configured this behavior in the configuration
files [src/api/config/packages/framework.yaml](../src/api/config/packages/framework.yaml) and 
[src/api/config/services.yaml](../src/api/config/services.yaml). The 
migration [src/api/migrations/Version20200424093138](../src/api/migrations/Version20200424093138.php) generates 
the `sessions` table. 

### Users' permissions

In Symfony, roles (i.e., `ROLE_FOO`) represent users' permissions.

In the application, we defined three hierarchical roles: administrator, merchant, and client.
Hierarchical means that:

1. The administrator is the top-level permission: it has its access level and the merchant and client's access levels.
2. A merchant has its access level but also the access level of the client.
3. A client has only its access level.

In other words, if you limit the access to a resource to users with the merchant role, 
administrator users can access it too but not the clients.

We configured this hierarchy in the configuration file [src/api/config/packages/security.yaml](../src/api/config/packages/security.yaml).

As explained in the previous chapter, we implemented the `getRoles` method from the `UserInterface`. 
This method has to return an array of string. 

However, our users have only one role attached to them (thanks to the hierarchy).
That's why we always return an array of one element. 

Moreover, we create the [src/api/src/Domain/Enum/Role](../src/api/src/Domain/Enum/Role.php) enumerator, which lists 
our users'`role` property's available values. These values don't have the prefix `ROLE_` because 
we don't want to store Symfony specific information in the `users` table. 

Yet, this prefix is mandatory because otherwise, Symfony will not recognize the permission.

That's why we prefix the role  whenever we interact with Symfony in our code. For instance, in annotations, 
or in the `getRoles` method. 

**Note:** a user must have one role; otherwise authentication won't work.

### Access control

Access control in the API is about defining what kind of users (anonymous, authenticated, administrator, etc.) 
may call (or not) an HTTP entry point.

In the API, there are three sorts :

1. Symfony's routes.
2. GraphQL mutations/queries.
3. The GraphQL fields.

#### Symfony routes' annotations

**Restrict to authenticated users:**

```php
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/download/foo", methods={"GET"})
 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
 */
public function downloadFoo(Request $request): Response
```

**Restrict to authenticated users with a specific role:**

```php
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/download/foo", methods={"GET"})
 * @Security("is_granted('ROLE_ADMINISTRATOR')")
 */
public function downloadFoo(Request $request): Response
```

See the [security](https://symfony.com/doc/current/security.html) and 
[annotations](https://symfony.com/doc/current/bundles/SensioFrameworkExtraBundle/annotations/security.html) documentations
from Symfony for more details.

#### GraphQL annotations

[GraphQLite](https://graphqlite.thecodingmachine.io/) provides many Symfony like annotations, 
**even if they differ slightly on some occasions**. The import statements are also different.

**Restrict to authenticated users:**

```php
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;

/**
 * @Mutation
 * @Logged
 */
public function updateFoo(
    string $foo
))
```

**Inject the authenticated user:**

```php
use TheCodingMachine\GraphQLite\Annotations\InjectUser;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;

/**
 * @Mutation
 * @Logged
 * @InjectUser(for="$user")
 */
public function updateFoo(
    User $user,
    string $foo
)
```

**Restrict to authenticated users with a specific role:**

```php
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Security;

/**
 * @Mutation
 * @Logged
 * @Security("is_granted('ROLE_ADMINISTRATOR')")
 */
public function updateFoo(
    string $foo
)
```

**Contrary to Symfony's routes, always put the `@Logged` annotation before the `@Security` and `@InjectUser` annotations 
on your GraphQL entry points. The web application needs to know the difference between unauthenticated (`401`) 
and access denied (`403`)!**

See [GraphQLite documentation](https://graphqlite.thecodingmachine.io/docs/fine-grained-security) for more details.

#### Symfony's voters

Sometimes it is not enough to restrict access to authenticated users/users with a specific role.
For instance, when a resource is only accessible to the user owning it.

That's when Symfony's voters come in handy!

It comes in two parts:

1. The PHP class which is specifying the voter's rules.
2. The annotation we put on GraphQL mutations/queries and Symfony's routes.

For instance, let's examine the following scenario: a merchant can update a product, but only if his company owns the
product.

```php
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Security;

/**
 * @Mutation
 * @Logged
 * @Security("is_granted('UPDATE_PRODUCT', product)")
 */
public function updateProduct(
    Product $product,
    string $name,
    float $price
): Product
```

A voter annotation has two arguments:

1. The attribute: in our application, it's equivalent to an action, i.e., `UPDATE_USER`, `GET_USER`, etc.
2. The subject: mostly the `Model` on which we want to check ownership.

Here the annotation asks for a voter that may handle the `UPDATE_PRODUCT` attribute for the `$product` subject.

By convention, we've created a voter PHP class per subject. In that case, as the subject is a
[src/api/src/Domain/Model/Product](../src/api/src/Domain/Model/Product.php), we've made the 
[src/api/src/Infrastructure/Security/Voter/ProductVoter](../src/api/src/Infrastructure/Security/Voter/ProductVoter.php).

Each voters' PHP class consist of three parts:

1. The attributes constants.
2. The method `supports`: it returns `true` if the voter supports both the given attribute and subject.
3. The method `voteOnAttribute`: only called if the `supports` method returned `true`. It contains your custom logic
for validating (or not) the access.

Take a closer look at those methods 
from [ProductVoter](../src/api/src/Infrastructure/Security/Voter/ProductVoter.php) for a better understanding.

**Note:** in your Symfony's routes, you may not have access to a `Model` directly but an `id` instead. 
The [src/api/src/Infrastructure/Controller/Order/OrderInvoiceController](../src/api/src/Infrastructure/Controller/Order/OrderInvoiceController.php)
and [src/api/src/Infrastructure/Security/Voter/OrderVoter](../src/api/src/Infrastructure/Security/Voter/OrderVoter.php)
show how to handle that kind of scenario.

#### GraphQL fields

Usually, you define your GraphQL types' fields in your migrations or your `Model`'s getters in the getters of your
 when overriding a base `Model`'s getter. That's when you must decide if you want to expose or not the field
to your GraphQL clients.

Also, as you are developing both the clients and the API, securing the entry points should be enough. If that's not the
case, you can add the same `@Security` annotations to your getters as the ones from the mutations/queries.

### CORS

CORS is the mechanism defining what can interact with the API via HTTP requests.

The application uses the [nelmio/cors-bundle](https://github.com/nelmio/NelmioCorsBundle) package for that task.
We configured this bundle in the configuration file [src/api/config/packages/nelmio_cors.yaml](../src/api/config/packages/nelmio_cors.yaml).

The current configuration only authorizes HTTP requests from the main domain (and the API subdomain).

**Note:** never use `*` as `allow origin` because it opens your API to the world. As there is no CSRF protection, a
malicious hacker will be able to hijack the connexion of one of your authenticated users to do bad things. Also, make sure
you don't have XSS vulnerabilities. 

## Web application

Security for the web application has many scopes:

1. Authenticate users.
2. Retrieve their information (email, first name, etc.)
3. Makes sure the GraphQL client gives the `PHPSESSID` cookie on server-side requests.
4. Display or not UI element.
5. Access control.

### `auth` store

The [src/webapp/store/auth](../src/webapp/store/auth) store centralizes the data of the authenticated user.

We use this store in many parts of the security process.

**The state:** [src/webapp/store/auth/state.js](../src/webapp/store/auth/state.js)

It contains a `user` object with the following values:

* `id`
* `firstName`
* `lastName`
* `email`
* `locale`
* `role`

We initialize these values with empty strings.

**Getters:** [src/webapp/store/auth/getters.js](../src/webapp/store/auth/getters.js)

* `isAuthenticated`: it returns `true` if the `user`'s `email` property from the state is empty. It might return `true`
even if the user has no more session in the API, but we will see below how to handle such a case.
* `isGranted`: it returns `true` if the user has the access level of the given role. It works as the `@Security` annotation
from the API.

It would be best to use these getters mostly for displaying (or not) an element in the UI.

**Mutations:** [src/webapp/store/auth/mutations.js](../src/webapp/store/auth/mutations.js)

* `setUser`: sets the state's `user` object.
* `setUserLocale`: sets the state's `user`'s `locale` property.
* `resetUser`: resets the state's `user` object with empty strings.

**Actions:** [src/webapp/store/auth/actions.js](../src/webapp/store/auth/actions.js)

* `me`: calls the [me GraphQL query](../src/webapp/services/queries/auth/me.query.js) and, according to the result, 
sets the state's `user` object or resets it.

### Role hierarchy

File [src/webapp/services/role-authorization-levels.js](../src/webapp/services/role-authorization-levels.js) exposes
the `level` method. It reproduces Symfony's roles' hierarchy.

### Authenticate users

On the [src/webapp/pages/login.vue](../src/webapp/pages/login.vue) page, 
we call the [login GraphQL mutation](../src/webapp/services/mutations/auth/login.mutation.js). On success, we feed the state
of the [src/webapp/store/auth](../src/webapp/store/auth) store, thanks to the `setUser` mutation.

As explained before, the API sets the `PHPSESSID` cookie in the browser. 

When in SPA mode, the browser sets the header `Cookie` with the `PHPSESSID` on each HTTP request to the API. 

However, the first time the user lands on the application, Nuxt.js server-renders the current page. It also renders pages
with the `asyncData` attribute on the server.

In the file [src/webapp/store/actions.js](../src/webapp/store/actions.js),
there is an `nuxtServerInit` method, which Nuxt.js calls before server-rendering every page. 
In this function, we:

1. Set the header `Cookie` for every server-side GraphQL requests.
2. Call the `me` action to fetch (or not) the user data (useful when the user refreshes the page).

### Access control

The [src/webapp/layouts/error.vue](../src/webapp/layouts/error.vue) layout handles almost every error.

You can propagate a GraphQL error via `context.error(e)` in the `asyncData` component's attribute or `this.$nuxt.error(e)`
in your component's methods (except mixins, where you have to throw it).

In the [error.vue](../src/webapp/layouts/error.vue) layout, we check if:

* `401` status code: the user has no session in the API. Therefore, we call the `resetUser` mutation and redirect the
user to the login page. On success, the web application redirects the user to the current page thanks to the `redirect`
query parameter. 
* `404` or `403`, or anything else: we display an error page.

Some pages are also not available for the authenticated user 
(for instance, the [src/webapp/pages/login.vue](../src/webapp/pages/login.vue) page). You may use the 
[src/webapp/middleware/redirect-if-authenticated.js](../src/webapp/middleware/redirect-if-authenticated.js) middleware
to redirect the user to the home page.

---

[Back to top](#security) - [Home](../README.md)