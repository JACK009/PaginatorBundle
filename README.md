This Bundle is using the Best Practices for Reusable Bundles https://symfony.com/doc/current/bundles/best_practices.html#bundle-name

Usage
=====
- Used in Symfony 8.x and PHP 8.4+ projects, this bundle provides a simple and flexible way to paginate large datasets.
- Bootstrap v5 compatible by default. (You can customize or creat your own Twig macro to fit other CSS frameworks.)
- Paginator DTO is returned to controllers and templates, containing pagination metadata and the current page's items.
- Provides a Twig macro for rendering pagination controls.
- Default implementation of `IPaginatorService` using Doctrine ORM's QueryBuilder.
- Easily customizable and extendable to fit specific project needs.
- Includes unit and functional tests to ensure reliability.
- See the [documentation reference](docs/index.md) for detailed class, method and implementation documentation.

Installation
============

Make sure Composer is installed globally, as explained in the
[installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Applications that use Symfony Flex
----------------------------------

Open a command console, enter your project directory and execute:

```console
composer require jack009/paginator-bundle
```

Applications that don't use Symfony Flex
----------------------------------------

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
composer require jack009/paginator-bundle
```

### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    jack009\PaginatorBundle\PaginatorBundle::class => ['all' => true],
];
```