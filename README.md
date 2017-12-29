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
    "urbics/civitools": "~1.*"
}
```

and `composer install`.

## Tools

 - Run civi:make:db to create a new civicrm database directly, using CiviCRM's civicrm.msql script.
 - Run civi:make:migration to generate migration files, optionally with seeder and model classes.  
 - Build the tables using Laravel's migration: `php artisan migrate --database=civicrm --path=database/migrations/civi --seed` (These are the default settings - change database connection and path as needed)

## Limitations and cautions
This is the same package as urbics/laracivi with the civi api functionality and the civicrm-core and civicrm-packages removed.  It is intended for projects that need direct access to a CiviCRM, but have dependency version conflicts with the civicrm-core package.

## Tests
The project includes phpunit tests for each of the console commands.
