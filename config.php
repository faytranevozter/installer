<?php 

// ENABLING SESSION
// $config['session'] = FALSE;
$config['session'] = 'something-beautiful';

// DATABASE CONNECTION CONFIG
$config['file']['db_connection']['path'] = 'config/function.php';
$config['file']['db_connection']['replace']['host'] = '{DB_HOSTNAME}';
$config['file']['db_connection']['replace']['user'] = '{DB_USERNAME}';
$config['file']['db_connection']['replace']['pass'] = '{DB_PASSWORD}';
$config['file']['db_connection']['replace']['database'] = '{DB_NAME}';

// DATABASE SQL CONFIG
$config['file']['sql']['path'] = 'config/structure.sql';
$config['file']['sql']['replace']['prefix'] = '{TABLE_PREFIX}';

// ACCOUNT NAME
$config['file']['account']['path'] = 'config/structure.sql';
$config['file']['account']['replace']['prefix'] = '{TB_PREFIX}';

// SITE CONFIG
$config['file']['site']['path'] = 'config/koneksi.php';
$config['file']['site']['replace']['prefix'] = '{TB_PREFIX}';

/* DEFAULT ROUTING */
$config['routes']['default'] = 'handler/first_landing';
$config['routes']['404'] = 'handler';

/* USER ROUTING */
$config['routes']['first-step'] = 'inside/first/first';
