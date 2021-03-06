
<p align="center">
    <a href="https://www.fusio-project.org/" target="_blank"><img src="https://www.fusio-project.org/img/fusio_64px.png"></a>
</p>

# About

Fusio is an open source API management platform which helps to build and manage 
RESTful APIs. We think that there is a huge potential in the API economy. 
Whether you need an API to expose your business functionality, build micro 
services, develop SPAs or Mobile-Apps. Because of this we think that Fusio is a 
great tool to simplify building such APIs. More information on 
https://www.fusio-project.org/

# Why

The originally idea of Fusio was to provide a tool which lets you easily build a
great API beside an existing application. I.e. in case you have already a web
application on a domain `acme.com` Fusio helps you to build the fitting API
at `api.acme.com`. Beside this use case you can also use Fusio to build a new 
API from scratch or use it internally i.e. for micro services.

To build the API Fusio can connect to different databases, message queue
systems or internal web services. There are also many ways to integrate your
[business logic](http://fusio.readthedocs.io/en/latest/development/business_logic.html)
into the API of Fusio.

# Features

Fusio covers all important aspects of the API lifecycle, so you can concentrate
on building the actual business logic of your API.

* __Versioning__  
  It is possible to define different versions of your endpoint. A concrete 
  version can be requested through the `Accept` header i.e. `application/vnd.acme.v1+json`
* __Documentation__  
  Fusio generates automatically a documentation of the API endpoints based on 
  the provided schema definitions.
* __Validation__  
  Fusio uses the standard JSONSchema to validate incoming request data
* __Authorization__  
  Fusio uses OAuth2 for API authorization. Each app can be limited to scopes to 
  request only specific endpoints of the API.
* __Analytics__  
  Fusio monitors all API activities and shows them on a dashboard so you always 
  know what is happening with your API.
* __Monetization__  
  Fusio provides a simple payment system to charge for specific routes.
* __Rate limiting__  
  It is possible to limit requests to a specific threshold.
* __Specification__  
  Fusio generates different specification formats for the defined API endpoints
  i.e. OpenAPI, Swagger, RAML.
* __Subscription__  
  Fusio contains a subscription layer which helps to build pub/sub for your API.
* __User management__  
  Fusio provides an API where new users can login or register a new account 
  through GitHub, Google, Facebook or through normal email registration.
* __Logging__  
  All errors which occur in your endpoint are logged and are visible at the 
  backend including all information from the request.
* __Connection__  
  Fusio provides an [adapter](https://www.fusio-project.org/adapter) system to
  connect to external services. By default we provide the HTTP and SQL 
  connection type but there are many other types available i.e. MongoDB, Amqp, 
  Cassandra.
* __Migration__  
  Fusio has a migration system which allows you to change the database schema
  on deployment.
* __Testing__  
  Fusio provides an api test case wherewith you can test every endpoint 
  response without setting up a local web server.

Basically with Fusio you only have to define the schema (request/response) of 
your API endpoints and implement the business logic. All other aspects are 
covered by Fusio.

# Development

Fusio provides two ways to develop an API. The first way is to build API 
endpoints only through the backend interface by using all available actions.
Through this you can solve already many tasks especially through the usage of
the [PHP-Sandbox](https://www.fusio-project.org/documentation/php) action.

The other way is to use the deploy-mechanism. Through this you can use normal
PHP files to implement your business logic and thus you can use the complete PHP 
ecosystem. Therefor you need to define a `.fusio.yml` 
[deploy file](https://fusio.readthedocs.io/en/latest/development/deploy.html)
which specifies the available routes and actions of the system. This file can be
deployed with the following command:

```
php bin/fusio deploy
```

The action of each route contains the source which handles the business logic. 
This can be i.e. a php class, a simple php file or an url. More information in
the [development doc](DEVELOPMENT.md). In the following an example action to
build an API response from a database:

```php
<?php

namespace App\Todo;

use Fusio\Engine\ActionAbstract;
use Fusio\Engine\ContextInterface;
use Fusio\Engine\ParametersInterface;
use Fusio\Engine\RequestInterface;

class Collection extends ActionAbstract
{
    public function handle(RequestInterface $request, ParametersInterface $configuration, ContextInterface $context)
    {
        /** @var \Doctrine\DBAL\Connection $connection */
        $connection = $this->connector->getConnection('System');

        $count   = $connection->fetchColumn('SELECT COUNT(*) FROM app_todo');
        $entries = $connection->fetchAll('SELECT * FROM app_todo WHERE status = 1 ORDER BY insertDate DESC LIMIT 16');

        return $this->response->build(200, [], [
            'totalResults' => $count,
            'entry' => $entries,
        ]);
    }
}

```

In the code we get the `System` connection which returns a
`\Doctrine\DBAL\Connection` instance. We have already 
[many adapters](https://www.fusio-project.org/adapter) to connect to different 
services. Then we simply fire some queries and return the response.

## Code-Generation

Fusio can be used in a lot of use cases. If you build an API based on entities
which should be stored in a relational database you can use our code generator
to simply build all routes, schemas and actions based on a simple YAML
definition. The code can be used as great starting point to rapidly build your
API. The tool is available at: https://generate.apioo.de/

# Internal API

If you install Fusio it provides the internal API. Through the internal API it
is possible to manage the complete system. This means you can create i.e.
routes, actions, schemas etc. Because of this Fusio has some reserved paths
which are needed by the system.

* `/backend`  
  Endpoints for the system configuration
* `/consumer`  
  Endpoints for the consumer i.e. register new accounts or create new apps 
* `/doc`  
  Endpoints for the documentation
* `/authorization`  
  Endpoints for the consumer to get i.e. information about the user itself and 
  to revoke an obtained access token
* `/export`  
  Endpoints to export the documentation into other formats i.e. swagger

There is also a complete [documentation](http://demo.fusio-project.org/internal/#!/page/about)
about all internal API endpoints.

# Apps

Since it is difficult to work with an API only app Fusio provides apps which
help to work with the API. Mostly apps are simple JS apps, which work with the
internal API of Fusio. You can see a list of all available apps at our
[marketplace](https://www.fusio-project.org/marketplace). You can install such
an app either through a CLI command i.e. `php bin/fusio marketplace:install fusio`
or through the backend app.

All apps are installed to the `apps/` folder. You need to tell Fusio the public
url to the apps folder at the `.env` file by defining the `FUSIO_APPS_URL`
variable. Depending on your setup this can be either a custom sub-domain like
`https://apps.acme.com` or simply the sub folder `https://acme.com/apps`.

## Backend

![Backend](https://github.com/apioo/fusio/blob/master/doc/_static/backend/routes.png)

The backend app is the main app to configure and manage your API. The installer
automatically installs this app. The app is located at `/apps/fusio/`.

# Installation

It is possible to install Fusio either through composer or manually file 
download.

## Composer

```
composer create-project fusio/fusio
```

## Download

https://github.com/apioo/fusio/releases

## Configuration

You can either manually install Fusio with the steps below or you can also use
the browser based installer at `public/install.php`. Note because of security
reasons it is highly recommended to remove the installer script after the
installation.

* __Adjust the configuration file__  
  Open the file `.env` in the Fusio directory and change the `FUSIO_URL` to
  the domain pointing to the public folder. Also insert the database credentials
  to the `FUSIO_DB_*` keys. Optional adjust `FUSIO_APPS_URL` to the public url
  of the apps folder (in case you want to use apps).
* __Execute the installation command__  
  The installation script inserts the Fusio database schema into the provided 
  database. It can be executed with the following command 
  `php bin/fusio install`.
* __Create administrator user__  
  After the installation is complete you have to create a new administrator 
  account. Therefor you can use the following command `php bin/fusio adduser`. 
  Choose as account type "Administrator".
* __Install backend app__  
  To manage your API through an admin panel you need to install the backend app.
  The app can be installed with the following command `php bin/fusio marketplace:install fusio`

You can verify the installation by visiting the `FUSIO_URL` with a browser. You
should see an API response that the installation was successful.

In case you want to install Fusio on a specific database you need to adjust the
`driver` parameter at the `configuration.php` file:

* `pdo_mysql`: MySQL
* `pdo_sqlite`: SQLite
* `pdo_pgsql`: PostgreSQL
* `sqlsrv`: Microsoft SQL Server
* `oci8`: Oracle
* `sqlanywhere`: SAP Sybase SQL Anywhere

## Docker

Alternatively it is also possible to setup Fusio through docker. This has the
advantage that you automatically get a complete running Fusio system without
configuration. This is especially great for testing and evaluation. To setup the
container you have to checkout the [repository](https://github.com/apioo/fusio-docker) 
and run the following command:

```
docker-compose up -d
```

This builds the Fusio system with a predefined backend account. The credentials 
are taken from the env variables `FUSIO_BACKEND_USER`, `FUSIO_BACKEND_EMAIL` 
and `FUSIO_BACKEND_PW` in the `docker-compose.yml`. If you are planing to run 
the container on the internet you must change these credentials.

# Documentation

Here we list all available documentation resources. If these resources dont
answer your questions or you want to provide feedback feel free to create an
issue on GitHub.

* [Getting started](https://www.fusio-project.org/bootstrap)  
* [Manual](https://fusio.readthedocs.io/en/latest/) 
* [Recipes](https://www.fusio-project.org/documentation/recipes) 
* [Videos](https://www.fusio-project.org/documentation/videos)
* [Backend API](http://demo.fusio-project.org/internal/#!/page/about)
* [PHP API](https://www.fusio-project.org/documentation/php)

# Use cases

Today there are many use cases where you need a great documented REST API. In 
the following we list the most popular choices where Fusio comes in to play.

## Business functionality

Exposing an API of your business functionality is a great way to extend your 
product. You enable customers to integrate it into other applications which
gives the possibility to open up for new markets. With Fusio you can build such 
APIs and integrate them seamlessly into your product. We also see many companies
which use the API itself as the core product.

## Micro services

With Fusio you can simply build small micro services which solve a specific task
in a complex system.

## Javascript applications

Javascript frameworks like i.e. AngularJS or EmberJS becoming the standard. With
Fusio you can easily build a backend for such applications. So you dont have to
build the backend part by yourself.

## Mobile apps

Almost all mobile apps need some form to interact with a remote service. This is
mostly done through REST APIs. With Fusio you can easily build such APIs which 
then can also be used by other applications.

# Contribution

Contributions to the project are always appreciated. There are many options
available to improve the project (which is not limited to coding). The following
list shows some ways how you can participate:

## Developing

If you are a PHP or Javascript developer you can help to improve the system. 
If you want to create a new feature it is in general recommended to create a new
issue where we can talk about the feature before you start to hack on it. So
there are three main components of Fusio:

**[Backend-API](https://github.com/apioo/fusio-impl)**

The backend API is the core of the system developed in PHP, which provides the
basic functionality of Fusio. This is the place to develop new core features and 
improvements.

**[Adapter](https://www.fusio-project.org/adapter)**

An adapter is a plugin to the Fusio system which can be used to connect to other
remote services. I.e. you could create a new adapter which speaks to a specific
API or other remote service. This is easy to develop since you can build it in
a separate repository. Please use the keyword `fusio-adapter` in your
`composer.json` file so that adapter gets listed automatically on our website.

**[Backend-App](https://github.com/apioo/fusio-backend)**

This is the AngularJS app which is used as GUI to control the backend. It is
the main app to improve the Fusio backend. But you are also free to develop new
apps for special use cases which talk to the internal API of Fusio.

## Testing

In general we have a high PHPUnit test case coverage and also automatic 
end-to-end AngularJS tests using protractor and selenium. Beside this it is
always great if users checkout the current master version of the project and try
to test every aspect of the system. In case you have found an issue please
report it through the issue tracker.

## Documentation

We want to create a system which is easy to use also by novice users. To enable
everybody to start using Fusio we need a simple to understand documentation.
Since we have not always the view of a novice developer please let us know about
chapters which are difficult to understand or topics which are missing. You can
also send us directly a pull request with an improved version. The main
documentation of Fusio is available at [readthedocs](http://fusio.readthedocs.io/en/latest/).
The documentation source is available in the `docs/` folder.

# Support

## Promotion

If you are a blogger or magazine we would be happy if you like to cover Fusio.
Please take a look at the Media section of our [About Page](https://www.fusio-project.org/about)
to download the official icon set. In case you have any questions please write
us a message directly so we can help you to create great content.

## Consulting

If you are a company or freelancer and want to get detailed information how you
can use Fusio you can contact us for consulting. In the workshop we try to find
the best way how you can use/integrate Fusio also we try to explain the
functionality and answer your questions.

## Donations

If this project helps you to generate revenue or in general if you like to
support the project you can donate any amount through paypal. We like to thank
every user who has donated to the project.

[![paypal](https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif)](https://www.paypal.me/fusioapi)
