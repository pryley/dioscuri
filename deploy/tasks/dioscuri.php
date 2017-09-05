<?php

namespace Deployer;

require dirname(__DIR__) . '/recipe/dioscuri/prepare.php';
require dirname(__DIR__) . '/recipe/dioscuri/pull.php';
require dirname(__DIR__) . '/recipe/dioscuri/push.php';
require dirname(__DIR__) . '/recipe/dioscuri/rollback.php';
require dirname(__DIR__) . '/recipe/dioscuri/symlink.php';
require dirname(__DIR__) . '/recipe/dioscuri/theme.php';
require dirname(__DIR__) . '/recipe/dioscuri/upload.php';

task( 'deploy', [
    'deploy:info', // show deploy info
    'deploy:prepare', // prepare the server for deployment
    'deploy:upload_env_file', // upload the stage-specific env file if needed
    'deploy:upload_shared_files', // upload the stage-specific shared files if needed
    'deploy:lock', // lock deployment
    'deploy:release', // create release
    'deploy:update_code', // git
    'deploy:vendors', // composer update
    'deploy:shared', // symlink shared dirs/files
    'deploy:writable', // set permissions for writable dirs
    'deploy:theme_vendors', // theme composer update
    'deploy:clear_paths', // remove extraneous dirs/files
    'deploy:theme', // deploy theme
    'deploy:symlink', // symlink the release
    'deploy:unlock', // unlock deployment
    'cleanup', // remove old releases
    'success',
])->desc( 'Deploy your project' );

task( 'pull', [
    'pull:info', // show task info
    'pull:uploads', // sync uploads from remote
    'pull:database', // import database from remote
    'pull:cleanup', // flush local permalinks
])->desc( 'Pull uploads and database to local' );

task( 'push', [
    'push:info', // show task info
    'push:uploads', // sync uploads to remote
    'push:database', // export database to remote
    'push:cleanup', // flush remote permalinks
])->desc( 'Push uploads and database to remote' );

task( 'firstrun', [
    'deploy', // deploy the application
    'push', // push uploads and database to remote
])->desc( 'Deploy for the first time' );
