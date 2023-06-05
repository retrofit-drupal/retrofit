<?php

declare(strict_types=1);

function theme($hook, $variables = array())
{
    // @todo phptemplate bridge.
    // @todo pre/post process of variables.
    $themeFunction = 'theme_' . $hook;
    if (function_exists($themeFunction)) {
        return $themeFunction($variables);
    }
}
