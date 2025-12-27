# PaginatorBundle

A small, framework-integrated paginator bundle for Symfony projects.

This documentation covers installation, configuration, usage examples (controller + Twig macro), API reference, translations, testing, and contribution guidelines.

ASSUMPTIONS

- This bundle targets Symfony 8.x and PHP 8.4+.
- The repository contains:
  - `src/DTO/Paginator.php` — paginator DTO returned to templates/controllers.
  - `src/Service/IPaginatorService.php` — service interface for pagination.
  - `src/Service/PaginatorService.php` — default implementation.
  - `templates/_macro/_paginator.html.twig` — Twig macro for rendering pagination controls.
  - `src/DependencyInjection/PaginatorExtension.php` — bundle configuration/wiring.

If your project uses older Symfony/PHP versions, you may need small syntax adjustments.

Quick links

- Installation
- Configuration
- Usage
  - Controller example
  - Twig macro example
- API reference
- Translations
- Testing
- Contributing


## Installation

Install the bundle with Composer (replace package/name with the real package name if different):

```bash
composer require jack009/paginator-bundle
```

Enable the bundle if your project requires manual registration (Symfony < 4.0). Modern Symfony applications using Flex typically register bundles automatically.

If you need to register it manually, add the bundle class to `config/bundles.php`:

```php
return [
    // ...
    App\PaginatorBundle\PaginatorBundle::class => ['all' => true],
];
```


## Compatibility & Requirements

- PHP 8.0+
- Symfony 8.x (examples use services.yaml and auto-wiring)
- Twig (for the macro)
- Doctrine ORM / QueryBuilder (most examples assume a Doctrine Query or QueryBuilder)


## Configuration

The bundle provides a service implementing `IPaginatorService`. The `PaginatorExtension` will usually autoconfigure the service and add Twig paths for the macro.

If you need to override the service or configure options, add the following to `config/services.yaml`:

```yaml
services:
    # Replace the service id or class with your own implementation if you want custom behavior
    App\Service\CustomPaginatorService:
        tags: ['paginator.service']
        arguments: ['@doctrine.orm.entity_manager']

    # Optionally override the default implementation
    PaginatorBundle\Service\IPaginatorService: '@App\Service\CustomPaginatorService'
```

Check `src/DependencyInjection/PaginatorExtension.php` for any available configuration keys the bundle exposes. If no user-facing config exists, the bundle will work with default settings out of the box.

You can also set parameters in services.yaml set the default implementation if you don't create a custom one:

```yaml
parameters:
  paginator.max_results: 10
  paginator.max_limit: 100
```

## Usage

This section shows the most common ways to use the paginator: from a controller (service call) and from Twig (macro rendering).

Contract (assumptions)

- `IPaginatorService` exposes a method like:

```php
public function paginate(Query $target, int $page = 1, int $limit = null, array $routeParameters = [], string $pageParameter = 'page'): \Your\Namespace\DTO\Paginator;
```

Where `$target` can be a Doctrine Query. The returned `Paginator` DTO contains metadata used by the optional Twig macro.

If your installed interface uses a different method name (for example `getPagination`), adapt the examples below accordingly. The examples use `paginate(...)` as it is a common convention.

Controller example (Twig-rendered page)

```php
// src/Controller/ProductController.php
namespace App\Controller;

use App\Repository\ProductRepository;
use PaginatorBundle\Service\IPaginatorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    private IPaginatorService $paginatorService;

    public function __construct(IPaginatorService $paginatorService)
    {
        $this->paginatorService = $paginatorService;
    }

    #[Route('/products', name: 'product_list')]
    public function list(ProductRepository $repo, Request $request): Response
    {
        $qb = $repo->createQueryBuilder('p')->orderBy('p.id', 'DESC');

        $page = $request->query->getInt('page', 1);
        $pageLimit = 50; // or fetch from config / request

        $paginator = $this->paginatorService->getPagination(
            $qb->getQuery(),
            'route_name',
            $page, //current page, default 1
            $pageLimit, //optional
            ['routeParameter1' => 'value'] //route parameters if needed,
            'page' //optional page parameter name in URL, default 'page'
        );

        return $this->render('product/list.html.twig', [
            'items' => $paginator->getItems(),
            'paginator' => $paginator, // pass the DTO to Twig for macro usage or use its data directly
        ]);
    }
}
```

Twig macro usage

The bundle ships a Twig macro you can import and call from your templates.

Example Twig template:

```twig
{# templates/product/list.html.twig #}
{% extends 'base.html.twig' %}

{% import '@PaginatorBundle/_macro/_paginator.html.twig' as paginatorMacro %}

{% block body %}
    <ul>
        {% for item in items %}
            <li>{{ item.name }}</li>
        {% else %}
            <li>No items found.</li>
        {% endfor %}
    </ul>

    {{ paginatorMacro.paginator(paginator) }} {# where 'paginator' variable is the DTO passed from controller #}
{% endblock %}
```

Macro notes

- Import path uses the bundle namespace. If the Twig path differs in your installation, import accordingly.
- The macro expects the DTO shape provided by `src/DTO/Paginator.php` (see API reference below).


## API Reference

Below is a compact reference for the main classes you will interact with. If signatures differ in your installed version, please consult `src/Service/IPaginatorService.php` and `src/DTO/Paginator.php`.

Paginator DTO (assumed public API)

- Class: PaginatorBundle\DTO\Paginator

Methods / Accessors (common):

- getMaxPages(): int — Maximum number of page links to show in the pagination control.
- getCurrentPage(): int — Current page number.
- getPageSize(): int — Number of items per page.
- getTotalResults(): int — Total number of items across all pages.
- getRouteName(): ?string — Suggested route name for pagination links (optional).
- getRouteParameters(): array — Additional parameters to include when building links.
- getPageParameter(): string — Query parameter name for the page number (default 'page').
- getItems(): array — Returns the items for the current page.

Service interface

- Interface: PaginatorBundle\Service\IPaginatorService

Common method (assumed):

- paginate(Query $queryBuilder, string $routeName, int $page = 1, ?int $limit = null, array $routeParameters = [], string $pageParameter = 'page'): PaginatorBundle\DTO\Paginator

Notes:
- `$queryBuilder` is a Doctrine Query.
- `$limit` is optional — if omitted, the service may use a default value.
- `$routeParameters` helps the Twig macro build links (route name or parameters).

## Translations

The bundle ships translations in `translations/paginator+intl-icu.en.yaml`. If you need to translate labels used in the Twig macro, add translations for the relevant keys in your app translation files or override the bundle translations.

Example (config/packages/translation.yaml):

```yaml
framework:
    translator:
        paths: ['%kernel.project_dir%/translations']
```


## Testing

If the repo contains `tests/`, run the test suite with PHPUnit (or the provided test runner):

```bash
# run tests with vendor phpunit if present
vendor/bin/phpunit
```

Add tests for your custom paginator behavior (edge cases, large datasets, invalid page numbers).


## Contributing

Contributions are welcome. When opening issues or pull requests, please:

- Provide a minimal reproducible example for bugs.
- Add or update tests when changing behavior.
- Follow PSR-12 coding style and include type hints where appropriate.

If you plan to change public APIs (DTO or service signatures), mention it in your PR and update documentation in `docs/`.


## Changelog & Versioning

Follow semantic versioning. Add a short changelog file or use GitHub releases. For breaking changes, bump the major version and clearly document migration steps.


## Edge cases & Notes

- Invalid page numbers: the service should normalize pages < 1 to page 1 and clamp pages > lastPage to lastPage.
- Large offsets: use Doctrine's setFirstResult/setMaxResults carefully with very large offsets; consider keyset pagination if performance matters.
- When paginating queries with GROUP BY or complex joins, ensure the query returns a correct total count (the service may use a COUNT(*) subquery).


## Troubleshooting

- Twig macro not found: ensure the bundle's `PaginatorExtension` registers the templates path, or import the macro by its relative path in your app templates.
- Wrong item count: verify the query used for counting matches the items query (no accidental filters missing).


## Example: Full minimal app wiring (services.yaml)

```yaml
services:
  _defaults:
    autowire: true
    autoconfigure: true

  App\Controller\:
    resource: '../src/Controller'
    tags: ['controller.service_arguments']

  PaginatorBundle\Service\IPaginatorService: '@PaginatorBundle\Service\PaginatorService' # default provided by the bundle
```


## Final notes

This documentation page is intended as a single quick-start guide.