<?php

namespace Deployer;

require 'recipe/common.php';
require __DIR__ . '/deploy/recipe/common.php';
require __DIR__ . '/deploy/tasks/dioscuri.php';

configList( 'deploy/config.yml' );
serverList( 'deploy/server.yml' );

after( 'deploy:failed', 'deploy:unlock' );

// dep deploy [stage]    - Deploy your project
// dep firstrun [stage]  - Deploy for the first time
// dep pull [stage]      - Pull uploads and database to local
// dep push [stage]      - Push uploads and database to remote
// dep rollback [stage]  - Rollback to previous release
// dep setup [stage]     - Setup the server
// dep theme [stage]     - Rsync the theme
