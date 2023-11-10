<?php

declare(strict_types=1);

namespace Retrofit\Drupal\Tests\Integration\Database;

use Drupal\Core\Database\Connection;
use Retrofit\Drupal\Tests\Integration\IntegrationTestCase;

final class DrupalWriteRecordTest extends IntegrationTestCase
{
    /**
     * @var string[]
     */
    protected static $modules = ['system', 'dblog'];

    public function testWritingRecord(): void
    {
        $this->installSchema('dblog', ['watchdog']);
        $record = [
            'uid' => 0,
            'type' => 'test',
            'message' => 'test',
            'variables' => serialize([]),
            'severity' => 0,
            'link' => '',
            'location' => '',
            'referer' => '',
            'hostname' => '',
            'timestamp' => time(),
        ];
        $result = drupal_write_record('watchdog', $record);
        self::assertEquals(1, $result);
        self::assertArrayHasKey('wid', $record);
        self::assertEquals(1, $record['wid']);
        $record['message'] = 'test2';
        $result = drupal_write_record('watchdog', $record, ['wid']);
        self::assertEquals(2, $result);
        self::assertArrayHasKey('wid', $record);
        self::assertEquals(1, $record['wid']);

        $database = $this->container->get('database');
        assert($database instanceof Connection);
        $records = $database
            ->select('watchdog', 'w')
            ->fields('w')
            ->execute()
            ?->fetchAll();
        self::assertEquals(
            [(object) ($record + ['wid' => 1])],
            $records
        );
    }
}
