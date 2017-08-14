<?php

namespace Deployer;

require dirname(__DIR__) . '/recipe/dioscuri/pull.php';
require dirname(__DIR__) . '/recipe/dioscuri/push.php';
require dirname(__DIR__) . '/recipe/dioscuri/rollback.php';
require dirname(__DIR__) . '/recipe/dioscuri/setup.php';
require dirname(__DIR__) . '/recipe/dioscuri/symlink.php';
require dirname(__DIR__) . '/recipe/dioscuri/theme.php';

task( 'setup', [
	'setup:check', // server checks
	'setup:prepare', // create intial dirs/files
	'setup:shared_dirs', // upload shared dirs
	'setup:shared_files', // upload shared files
	'setup:composer', // self-update composer
])->desc( 'Setup the server' );

task( 'deploy', [
	'deploy:lock', // lock deployment
	'deploy:release', // create release
	'deploy:update_code', // git
	'deploy:vendors', // composer update
	'deploy:shared', // symlink shared dirs/files
	'deploy:writable', // set permissions for writable dirs
	'deploy:theme_vendors', // theme composer update
	'deploy:theme', // deploy theme
	'deploy:symlink', // symlink the release
	'deploy:clear_paths', // remove extraneous dirs/files
	'deploy:unlock', // unlock deployment
	'cleanup', // remove old releases
	'success',
])->desc( 'Deploy your project' );

task( 'pull', [
	'pull:uploads', // sync uploads from remote
	'pull:database', // import database from remote
	'pull:cleanup', // flush local permalinks
])->desc( 'Pull uploads and database to local' );

task( 'push', [
	'push:uploads', // sync uploads to remote
	'push:database', // export database to remote
	'push:cleanup', // flush remote permalinks
])->desc( 'Push uploads and database to remote' );

task( 'firstrun', [
	'setup', // setup the server
	'deploy', // deploy the application
	'push', // push uploads and database to remote
])->desc( 'Deploy for the first time' );
