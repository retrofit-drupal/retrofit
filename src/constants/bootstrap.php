<?php

declare(strict_types=1);

use Drupal\Core\Logger\RfcLogLevel;

const CHECK_PLAIN = 0;

const PASS_THROUGH = -1;

const WATCHDOG_EMERGENCY = RfcLogLevel::EMERGENCY;

const WATCHDOG_ALERT = RfcLogLevel::ALERT;

const WATCHDOG_CRITICAL = RfcLogLevel::CRITICAL;

const WATCHDOG_ERROR = RfcLogLevel::ERROR;

const WATCHDOG_WARNING = RfcLogLevel::WARNING;

const WATCHDOG_NOTICE = RfcLogLevel::NOTICE;

const WATCHDOG_INFO = RfcLogLevel::INFO;

const WATCHDOG_DEBUG = RfcLogLevel::DEBUG;
