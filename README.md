# Civitools
## Tools to integrate CiviCRM a database into a Laravel project.

Console commands to build migrations for all CiviCRM tables from CiviCRM's schema.xml source (as of 4.7) and seeders from civicrm_data.mysql and civicrm_acl.mysql source, as well as Entity model classes for all CiviCRM tables; also includes console commands to generate a CiviCRM database directly from civicrm.mysql, and to backup and restore a CiviCRM database.

## Package Installation
```sh
composer require urbics/civitools
```
Or manually modify `composer.json`:
``` json
"require": {
    "urbics/civitools": "~0.1"
}
```

and `composer install`.

Installer adds CIVI_DB_XXX settings to the .env file that should be reviewed to match your setup.  The tools use these settings to connect to the database.

## Tools

 - `civi:make:db` to create db and build tables using CiviCRM's civicrm.msql script.
 - `civi:make:migration` to generate Laravel migration files, optionally with seeder and model classes.  Note: you may need to increase the file limit above 256.  On Mac OS: `ulimit -n 1024`.
 - Build the tables using Laravel's migration: `php artisan migrate --database=civicrm --path=database/migrations/civi --seed` (These are the default settings - change database connection and path as needed).
 - `civi:db:backup` to back up or restore the civicrm database.  Be sure to set CIVI_DB_CONNECTION, CIVI_DB_DATABASE, CIVI_DB_HOST, CIVI_DB_USERNAME and CIVI_DB_PASSWORD in your .env file if any of the civicrm database settings are different from the default settings for your project.

## Limitations and cautions
This is the same package as urbics/laracivi with the civi api functionality and the civicrm-core and civicrm-packages removed.  It is intended for projects that need direct access to a CiviCRM, but have dependency version conflicts with the civicrm-core package.

## Tests
The project includes phpunit tests for each of the console commands.
