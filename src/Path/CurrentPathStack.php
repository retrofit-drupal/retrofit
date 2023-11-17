<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Path;

use Drupal\Core\Path\CurrentPathStack as CoreCurrentPathStack;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class CurrentPathStack extends CoreCurrentPathStack
{
    public function __construct(RequestStack $request_stack)
    {
        parent::__construct($request_stack);
        if ($request = $request_stack->getCurrentRequest()) {
            $_GET['q'] = $request->getPathInfo();
            $this->paths[$request] = ['path' => &$_GET['q']];
        }
    }

    public function getPath(?Request $request = null): string
    {
        if (!isset($request)) {
            $request = $this->requestStack->getCurrentRequest();
        }
        if (!isset($this->paths[$request])) {
            $this->paths[$request] = ['path' => $request->getPathInfo()];
        }
        return $this->paths[$request]['path'];
    }

    public function setPath($path, ?Request $request = null): static
    {
        if (!isset($request)) {
            $request = $this->requestStack->getCurrentRequest();
        }
        $this->paths[$request] = ['path' => $path];

        return $this;
    }
}
