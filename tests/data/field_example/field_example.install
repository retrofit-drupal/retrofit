<?php
/**
 * @file
 * Install, update, and uninstall functions for the field_example module.
 */

/**
 * Implements hook_field_schema().
 *
 * Defines the database schema of the field, using the format used by the
 * Schema API.
 *
 * The data we will store here is just one 7-character element, even
 * though the widget presents the three portions separately.
 *
 * All implementations of hook_field_schema() must be in the module's
 * .install file.
 *
 * @see http://drupal.org/node/146939
 * @see schemaapi
 * @see hook_field_schema()
 * @ingroup field_example
 */
function field_example_field_schema($field) {
  $columns = array(
    'rgb' => array('type' => 'varchar', 'length' => 7, 'not null' => FALSE),
  );
  $indexes = array(
    'rgb' => array('rgb'),
  );
  return array(
    'columns' => $columns,
    'indexes' => $indexes,
  );
}
