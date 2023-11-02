<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Tests\Integration;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\KernelTests\KernelTestBase;

final class DrupalSetMessageTest extends KernelTestBase
{
    public function testDrupalSetMessage(): void
    {
        // phpcs:ignore
        drupal_set_message(t('foo'), 'status');
        drupal_set_message(new FormattableMarkup('bar', []), 'warning');
        drupal_set_message(new TranslatableMarkup('baz'), 'error');

        $messenger = $this->container->get('messenger');
        self::assertInstanceOf(MessengerInterface::class, $messenger);
        self::assertEquals([
          'status' => ['foo'],
          'warning' => ['bar'],
          'error' => ['baz'],
        ], $messenger->all());
        self::assertEquals([
          'status' => ['foo'],
          'warning' => ['bar'],
          'error' => ['baz'],
        ], drupal_set_message());
    }
}
