<?php

return [
    'include-tables' => [],
    'exclude-tables' => [],
    // none, bzip2, none
    'compress' => 'gzip',
    'init_commands' => [],
    'no-data' => [],
    'reset-auto-increment' => false,
    'add-drop-database' => false,
    'add-drop-table' => false,
    'add-drop-trigger' => true,
    'add-locks' => true,
    'complete-insert' => false,
    'databases' => false,
    'default-character-set' => 'utf8',
    'disable-keys' => true,
    'extended-insert' => true,
    'events' => false,
    'hex-blob' => true, /* faster than escaped content */
    'net_buffer_length' => 1000000,
    'no-autocommit' => true,
    'no-create-info' => false,
    'lock-tables' => true,
    'routines' => false,
    'single-transaction' => true,
    'skip-triggers' => false,
    'skip-tz-utc' => false,
    'skip-comments' => false,
    'skip-dump-date' => false,
    'skip-definer' => false,
    'where' => '',
];