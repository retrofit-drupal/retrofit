<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Controller;

use Drupal\Core\Controller\TitleResolverInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;

final class RetrofitTitleResolver implements TitleResolverInterface
{
    /**
     * @var array<string, string>
     */
    private array $storedTitle = [];

    public function __construct(
        private readonly TitleResolverInterface $inner,
    ) {
    }

    public function setStoredTitle(string $title, Request $request): void
    {
        $this->storedTitle[$request->getPathInfo()] = $title;
    }

    public function getTitle(Request $request, Route $route): array|string|\Stringable|null
    {
        $path = $request->getPathInfo();
        if (isset($this->storedTitle[$path])) {
            return $this->storedTitle[$path];
        }
        return $this->inner->getTitle($request, $route);
    }
}
