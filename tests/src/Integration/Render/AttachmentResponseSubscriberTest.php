<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Tests\Integration\Render;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\Render\AttachmentsInterface;
use mglaman\DrupalTestHelpers\RequestTrait;
use mglaman\DrupalTestHelpers\TestHttpKernelTrait;
use Retrofit\Drupal\Tests\Integration\IntegrationTestCase;
use Symfony\Component\HttpFoundation\Request;

final class AttachmentResponseSubscriberTest extends IntegrationTestCase
{
    use RequestTrait;
    use TestHttpKernelTrait;

    protected static $modules = [
        'system',
    ];

    protected static function getTestModules(): array
    {
        return ['retrofit_fixtures'];
    }

    public function register(ContainerBuilder $container): void
    {
        parent::register($container);
        $this->registerTestHttpKernel($container);
    }

    public function testLibraryAttached(): void
    {
        $response = $this->doRequest(Request::create('/retrofit/drupal_add_library'));
        self::assertInstanceOf(AttachmentsInterface::class, $response);
        $attachments = $response->getAttachments();
        self::assertArrayHasKey('library', $attachments);
        self::assertContains('core/jquery', $attachments['library']);
        self::assertStringContainsString(
            '/core/assets/vendor/jquery/jquery.min.js',
            $this->getRawContent(),
        );
    }
}
