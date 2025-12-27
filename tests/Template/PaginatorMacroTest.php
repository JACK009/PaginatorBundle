<?php

namespace Jack009\Tests\Template;

use Jack009\PaginatorBundle\DTO\Paginator;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;
use Twig\TwigFunction;

class PaginatorMacroTest extends TestCase
{
    private Environment $twig;

    protected function setUp(): void
    {
        $loader = new FilesystemLoader(__DIR__ . '/../../templates');
        $this->twig = new Environment($loader);

        // Dummy 'trans' filter: basic parameter replacement and handling for paginationFromTo key
        $trans = new TwigFilter('trans', function ($message, $parameters = [], $domain = null) {
            if ($message === 'paginationFromTo') {
                $from = $parameters['{from}'] ?? 0;
                $to = $parameters['{to}'] ?? 0;
                $total = $parameters['{totalResults}'] ?? 0;

                return sprintf('%d to %d of %d', $from, $to, $total);
            }

            // default: if parameters provided, attempt to replace placeholders
            foreach ($parameters as $k => $v) {
                $message = str_replace($k, (string) $v, $message);
            }

            return $message;
        });

        $this->twig->addFilter($trans);

        // Dummy 'path' function to generate URLs used in macro
        $path = new TwigFunction('path', function (string $route, array $params = []) {
            if (empty($params)) {
                return '/' . $route;
            }

            return '/' . $route . '?' . http_build_query($params);
        });

        $this->twig->addFunction($path);
    }

    public function testMacroRendersBasicPagination(): void
    {
        $dto = new Paginator(5, 1, 10, 45, 'route_name', [], 'page', []);

        $template = "{% import '_macro/_paginator.html.twig' as paginatorMacro %}{{ paginatorMacro.paginator(pagination) }}";

        $html = $this->twig->createTemplate($template)->render(['pagination' => $dto]);

        // previous should be disabled on first page
        $this->assertStringContainsString('page-item disabled', $html);

        // should contain page links for 1 and 5 (allow surrounding whitespace)
        $this->assertMatchesRegularExpression('/>\s*1\s*</', $html);
        $this->assertMatchesRegularExpression('/>\s*5\s*</', $html);

        // should show total results somewhere
        $this->assertStringContainsString('45', $html);
    }

    public function testMacroRendersEllipsesForManyPages(): void
    {
        $dto = new Paginator(50, 25, 10, 500, 'route_name', [], 'page', []);

        $template = "{% import '_macro/_paginator.html.twig' as paginatorMacro %}{{ paginatorMacro.paginator(pagination) }}";

        $html = $this->twig->createTemplate($template)->render(['pagination' => $dto]);

        $this->assertStringContainsString('...', $html);
        $this->assertMatchesRegularExpression('/>\s*23\s*</', $html);
        $this->assertMatchesRegularExpression('/>\s*27\s*</', $html);
    }
}

