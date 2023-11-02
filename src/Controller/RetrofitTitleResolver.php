<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Controller;

use Drupal\Core\Controller\TitleResolverInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;

final class RetrofitTitleResolver implements TitleResolverInterface
{
    private string $storedTitle = '';

    public function __construct(
        private readonly TitleResolverInterface $inner,
    ) {
    }

    public function setStoredTitle(string $title): void
    {
        $this->storedTitle = $title;
    }

    public function getTitle(Request $request, Route $route): array|string|\Stringable|null
    {
        if ($this->storedTitle !== '') {
            return $this->storedTitle;
        }
        return $this->inner->getTitle($request, $route);
    }
}
