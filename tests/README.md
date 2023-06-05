# Tests

## Data fixtures

The `data` directory contains Drupal 7 code fixtures for testing the library.

Most fixtures are copied from the [Drupal 7 Examples module](https://git.drupalcode.org/project/examples/-/tree/7.x-1.x).

## PHPUnit test suites

* `Integration` tests extend `\Drupal\KernelTests\KernelTestBase` and integrate with a Drupal kernel.
* `Unit` tests extend `\PHPUnit\Framework\TestCase` and do not integrate with a Drupal kernel.

The `Utils` namespace is for utility classes to aid integration tests.
