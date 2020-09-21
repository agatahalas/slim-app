# Slim Framework 4 Skeleton Application

[![Coverage Status](https://coveralls.io/repos/github/slimphp/Slim-Skeleton/badge.svg?branch=master)](https://coveralls.io/github/slimphp/Slim-Skeleton?branch=master)

Use this skeleton application to quickly setup and start working on a new Slim Framework 4 application. This application uses the latest Slim 4 with Slim PSR-7 implementation and PHP-DI container implementation. It also uses the Monolog logger.

This skeleton application was built for Composer. This makes setting up a new Slim Framework application quick and easy.

## Install the Application

Run this command from the directory in which you want to install your new Slim Framework application.

```bash
composer create-project slim/slim-skeleton [my-app-name]
```

Replace `[my-app-name]` with the desired directory name for your new application. You'll want to:

* Point your virtual host document root to your new application's `public/` directory.
* Ensure `logs/` is web writable.

To run the application in development, you can run these commands 

```bash
cd [my-app-name]
composer start
```

Or you can use `docker-compose` to run the app with `docker`, so you can run these commands:
```bash
cd [my-app-name]
docker-compose up -d
```
After that, open `http://localhost:8080` in your browser.

Run this command in the application directory to run the test suite

```bash
composer test
```

That's it! Now go build something cool.

## Migration
### Migrate tables:
php vendor/bin/doctrine orm:schema-tool:create

### Migrate data:
 - Categories
 php migrations/CategoriesMigration.php
 - Icons
 php migrations/IconsMigration.php

## Getting data
### Getting icons
Getting specific icon with id 50:
http://slim-app.lndo.site/icon/50

Getting specific icon with different color. You can either use hex code for color or use of one css colors defined - here is the list(https://en.wikipedia.org/wiki/Lists_of_colors) of colors to use, just use its machine name with lowercase:
http://slim-app.lndo.site/icon/50?color=red
http://slim-app.lndo.site/icon/50?color=#f00000

Getting list of icons:
Table: http://slim-app.lndo.site/admin/icons
JSON: http://slim-app.lndo.site/api/icons
