<?php

namespace Deployer;

/**
 * 1. Change "current" to {{public_dir}}
 * 2. Append {{public_dir}} to release path
 */
desc( 'Creating symlink to release' );
task( 'deploy:symlink', function() {
    if( run( 'if [[ "$(man mv)" =~ "--no-target-directory" ]]; then echo "true"; fi' )->toBool() ) {
        // [1,2]
        // We are also only linking the {{public_dir}} in the release dir, NOT the whole release
        run( 'mv -T {{deploy_path}}/release/{{public_dir}} {{deploy_path}}/{{public_dir}}' );
    }
    else {
        // [1,2]
        // We are also only linking the {{public_dir}} in the release dir, NOT the whole release
        run( '{{bin/symlink}} {{release_path}}/{{public_dir}} {{deploy_path}}/{{public_dir}}' );
        run( 'cd {{deploy_path}} && rm release' );
    }
});
