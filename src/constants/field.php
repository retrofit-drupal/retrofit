<?php

use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Entity\EntityStorageInterface;

const FIELD_CARDINALITY_UNLIMITED = FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED;

const FIELD_BEHAVIOR_NONE = 0x1;

const FIELD_BEHAVIOR_DEFAULT = 0x2;

const FIELD_BEHAVIOR_CUSTOM = 0x4;

const FIELD_LOAD_CURRENT = EntityStorageInterface::FIELD_LOAD_CURRENT;

const FIELD_LOAD_REVISION = EntityStorageInterface::FIELD_LOAD_REVISION;
