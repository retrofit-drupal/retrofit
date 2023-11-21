<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Tests\Integration\Language;

use mglaman\DrupalTestHelpers\RequestTrait;
use Retrofit\Drupal\Tests\Integration\IntegrationTestCase;
use Symfony\Component\HttpFoundation\Request;

final class GlobalLanguageContentTest extends IntegrationTestCase
{
    use RequestTrait;

    public function testGlobalLanguageContent(): void
    {
        $this->doRequest(Request::create('/'));
        $this->assertArrayHasKey('language', $GLOBALS);
        self::assertEquals('en', $GLOBALS['language']->language);
    }
}
