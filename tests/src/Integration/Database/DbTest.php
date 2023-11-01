<?php

namespace Retrofit\Drupal\Tests\Integration\Database;

use Drupal\KernelTests\KernelTestBase;

class DbTest extends KernelTestBase
{
    protected const TABLE_NAME = 'test_table';

    public function setUp(): void
    {
        parent::setUp();
        $database = $this->container->get('database');
        $database->schema()->createTable(self::TABLE_NAME, $this->schemaForTestTable());
        $database->insert(self::TABLE_NAME)
        ->fields(['sample' => 'foo'])
        ->execute();
        $database->insert(self::TABLE_NAME)
        ->fields(['sample' => 'bar'])
        ->execute();
        $database->insert(self::TABLE_NAME)
        ->fields(['sample' => 'baz'])
        ->execute();
    }

    public function testDbQuery(): void
    {
        $result = db_query('SELECT sample FROM {test_table}')->fetchAll();
        self::assertEquals([
        (object) ['sample' => 'foo'],
        (object) ['sample' => 'bar'],
        (object) ['sample' => 'baz'],
        ], $result);
    }

  /**
   * @dataProvider dbDeleteData
   */
    public function testDbDelete(int $expectedCount, array $conditions): void
    {
        $database = $this->container->get('database');
        $beforeCount = $database->select(self::TABLE_NAME)
        ->countQuery()
        ->execute()
        ->fetchField();
        self::assertEquals(3, $beforeCount);
        $db_delete = db_delete(self::TABLE_NAME);
        foreach ($conditions as $condition) {
            $db_delete->condition($condition[0], $condition[1] ?? null, $condition[2] ?? '=');
        }
        $db_delete->execute();
        $afterCount = $database->select(self::TABLE_NAME)
        ->countQuery()
        ->execute()
        ->fetchField();
        self::assertEquals($expectedCount, $afterCount);
    }

    public static function dbDeleteData()
    {
        yield [0, []];
        yield [1, [['sample', 'foo', '<>']]];
        yield [2, [['sample', 'bar', '=']]];
    }

    public function testDbSelect(): void
    {
        $one = db_select(self::TABLE_NAME, 't')
        ->fields('t', ['sample'])
        ->execute()->fetchAll();
        self::assertEquals([
        (object) ['sample' => 'foo'],
        (object) ['sample' => 'bar'],
        (object) ['sample' => 'baz'],
        ], $one);
        $two = db_select(self::TABLE_NAME, 't')
        ->fields('t', ['sample'])
        ->orderBy('id', 'DESC')
        ->execute()->fetchAll();
        self::assertEquals([
        (object) ['sample' => 'baz'],
        (object) ['sample' => 'bar'],
        (object) ['sample' => 'foo'],
        ], $two);
        $three = db_select(self::TABLE_NAME, 't')
        ->fields('t', ['sample'])
        ->condition('sample', 'foo')
        ->execute()->fetchAll();
        self::assertEquals([
        (object) ['sample' => 'foo'],
        ], $three);
    }

    public function testDbSelectWithTarget(): void
    {
        $one = db_select(self::TABLE_NAME, 't', ['target' => 'replica'])
          ->fields('t', ['sample'])
          ->execute()->fetchAll();
        self::assertEquals([
          (object)['sample' => 'foo'],
          (object)['sample' => 'bar'],
          (object)['sample' => 'baz'],
        ], $one);
    }

    public function testDbLike(): void
    {
        $query = db_select(self::TABLE_NAME, 't')
        ->fields('t', ['sample'])
        ->condition('sample', db_like('ba') . '%', 'LIKE')
        ->execute()->fetchAll();
        self::assertEquals([
        (object) ['sample' => 'bar'],
        (object) ['sample' => 'baz'],
        ], $query);
    }

    public function testDbAnd(): void
    {
        $query = db_select(self::TABLE_NAME, 't')
        ->fields('t', ['sample'])
        ->condition(
            db_and()
            ->condition('sample', 'foo', '<>')
            ->condition('sample', 'bar', '<>')
        )
        ->execute()->fetchAll();
        self::assertEquals([
        (object) ['sample' => 'baz'],
        ], $query);
    }

    public function testDbTableExists(): void
    {
        self::assertTrue(db_table_exists(self::TABLE_NAME));
        self::assertFalse(db_table_exists('non_existent_table'));
    }


    protected function schemaForTestTable(): array
    {
        return [
        'fields' => [
        'id' => [
          'type' => 'serial',
          'unsigned' => true,
          'not null' => true,
        ],
        'sample' => [
          'type' => 'varchar',
          'not null' => false,
          'default' => '',
        ],
        ],
        ];
    }
}
