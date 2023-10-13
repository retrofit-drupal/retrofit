<?php

use Drupal\Core\File\FileSystemInterface;

const FILE_CREATE_DIRECTORY = FileSystemInterface::CREATE_DIRECTORY;

const FILE_MODIFY_PERMISSIONS = FileSystemInterface::MODIFY_PERMISSIONS;

const FILE_EXISTS_RENAME = FileSystemInterface::EXISTS_RENAME;

const FILE_EXISTS_REPLACE = FileSystemInterface::EXISTS_REPLACE;

const FILE_EXISTS_ERROR = FileSystemInterface::EXISTS_ERROR;

// Moved to FileInterface, which is only available after file module loads.
const FILE_STATUS_PERMANENT = 1;

define('FILE_INSECURE_EXTENSIONS', implode('|', FileSystemInterface::INSECURE_EXTENSIONS));
