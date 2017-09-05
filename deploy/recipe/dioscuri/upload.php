<?php

namespace Deployer;

use Deployer\Task\Context;

desc('Upload env file');
task('deploy:upload_env_file', function () {
    cd('{{deploy_path}}');
    // upload stage-specific environment file if needed
    if( !test( '[ ! -f {{deploy_path}}/env.php ]' ))return;
    foreach( [sprintf( 'env.%s.php', get( 'stage' )), 'env.example.php'] as $file ) {
        if( is_file( $file )) {
            upload( $file, '{{deploy_path}}/env.php' );
            break;
        }
    }
});

desc('Upload shared files');
task('deploy:upload_shared_files', function () {
    $dirName = get( 'deploy_path' ) . '/shared/';
    foreach( get( 'shared_files' ) as $file ) {
        $fileInfo = pathinfo( $file );
        if( empty( $fileInfo['filename'] ) && !empty( $fileInfo['extension'] )) {
            $stagingFile = sprintf( '%s.%s', $file, get( 'stage' ));
        }
        else {
            $stagingFile = sprintf( '%s%s.%s',
                substr( $file, 0, strlen( $file ) - strlen( $fileInfo['extension'] )),
                get( 'stage' ),
                $fileInfo['extension']
            );
        }
        if( !test( '[ ! -f $(echo $dirName$file) ]' ))return;
        if( is_file( $stagingFile )) {
            upload( $stagingFile, $dirName . $file );
        }
        else if( is_file( $file )) {
            upload( $file, $dirName . $file );
        }
    }
});
