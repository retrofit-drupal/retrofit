<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Path;

use Drupal\Core\Path\CurrentPathStack as CoreCurrentPathStack;
use Symfony\Component\HttpFoundation\Request;

class CurrentPathStack extends CoreCurrentPathStack
{
    public function getPath(?Request $request = null): string
    {
        $request ??= $this->requestStack->getCurrentRequest();
        assert($request instanceof Request);
        $this->paths[$request] ??= ['path' => $request->getPathInfo()];
        assert(is_array($this->paths[$request]) && is_string($this->paths[$request]['path']));
        return $this->paths[$request]['path'];
    }

    public function setPath($path, ?Request $request = null): static
    {
        $request ??= $this->requestStack->getCurrentRequest();
        assert($request instanceof Request);
        $_GET['q'] = $path;
        $this->paths[$request] = ['path' => &$_GET['q']];
        return $this;
    }
}
