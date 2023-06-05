<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Tests\Integration\Routing;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Tests\user\Traits\UserCreationTrait;
use mglaman\DrupalTestHelpers\RequestTrait;
use mglaman\DrupalTestHelpers\TestHttpKernelTrait;
use Retrofit\Drupal\Tests\Integration\IntegrationTestCase;
use Symfony\Component\HttpFoundation\Request;

final class HookMenuFormRoutesTest extends IntegrationTestCase
{
    use RequestTrait;
    use TestHttpKernelTrait;
    use UserCreationTrait;

    protected static $modules = [
      'system',
      'user',
    ];

    protected static function getTestModules(): array
    {
        return ['form_example'];
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->config('system.site')
          ->set('name', 'Drupal')
          ->save();
    }

    public function testTutorialOne(): void
    {
        $response = $this->doRequest(Request::create('/examples/form_example/tutorial/1'));
        $this->assertTitle('#1 | Drupal');
        self::assertStringContainsString(
            'A form with nothing but a textfield',
            $response->getContent()
        );
        $input = $this->cssSelect('input[name="name"]');
        self::assertCount(1, $input);
    }

    public function testTutorialTwo(): void
    {
        $response = $this->doRequest(Request::create('/examples/form_example/tutorial/2'));
        $this->assertTitle('#2 | Drupal');
        self::assertStringContainsString(
            'A simple form with a submit button',
            $response->getContent()
        );
        $input = $this->cssSelect('input[name="name"]');
        self::assertCount(1, $input);
        $input = $this->cssSelect('input[name="op"]');
        self::assertCount(1, $input);
    }

    public function testTutorialSix(): void
    {
        $path = '/examples/form_example/tutorial/6';
        $this->doFormSubmit($path, [
          'first' => 'Johnny',
          'last' => 'Appleseed',
          'year_of_birth' => '1970',
          'op' => 'Submit',
        ]);
        self::assertStringNotContainsString(
            'Enter a year between 1900 and 2000.',
            $this->getTextContent(),
        );
        $this->doFormSubmit($path, [
          'first' => 'Johnny',
          'last' => 'Appleseed',
          'year_of_birth' => '2023',
          'op' => 'Submit',
        ]);
        self::assertStringContainsString(
            'Enter a year between 1900 and 2000.',
            $this->getTextContent(),
        );
    }

    public function testTutorialSeven(): void
    {
        $path = '/examples/form_example/tutorial/7';
        $this->doFormSubmit($path, [
          'first' => 'Johnny',
          'last' => 'Appleseed',
          'year_of_birth' => '1970',
          'op' => 'Submit',
        ]);
        self::assertStringContainsString(
            'The form has been submitted. name="Johnny Appleseed", year of birth=1970',
            $this->getTextContent(),
        );
    }
}
