<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Controller;

use Drupal\Core\Controller\TitleResolverInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Route;

final class RetrofitTitleResolver implements TitleResolverInterface
{
    /**
     * @var \SplObjectStorage<Request, string>
     */
    private $titles;

    public function __construct(
        private readonly TitleResolverInterface $inner,
        private readonly RequestStack $requestStack,
    ) {
        $this->titles = new \SplObjectStorage();
    }

    public function setStoredTitle(string $title): void
    {
        $this->titles[$this->requestStack->getCurrentRequest()] = $title;
    }

    public function getTitle(Request $request, Route $route): array|string|\Stringable|null
    {
        if (isset($this->titles[$request])) {
            return $this->titles[$request];
        }
        return $this->inner->getTitle($request, $route);
    }
}
