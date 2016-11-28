<?php 
// SITE CONFIG
$config['file']['config']['path'] = './file/koneksi.php';
$config['file']['config']['replace']['host'] = '{DB_HOSTNAME}';
$config['file']['config']['replace']['user'] = '{DB_USERNAME}';
$config['file']['config']['replace']['pass'] = '{DB_PASSWORD}';
$config['file']['config']['replace']['database'] = '{DB_NAME}';

// DATABASE SQL CONFIG
$config['file']['sql']['path'] = './file/structure.sql';
$config['file']['sql']['replace']['prefix'] = '{TB_PREFIX}';

$config['routes']['default'] = 'first/index';
$config['routes']['404'] = 'inside/first/defdef';

$config['routes']['login'] = 'inside/first/signin';
