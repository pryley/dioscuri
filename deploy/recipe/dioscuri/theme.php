<?php

namespace Deployer;

use Deployer\Task\Context;

desc( 'Deploy theme' );
task( 'deploy:theme', function() {
	$server = Context::get()->getServer()->getConfiguration();
	$user = $server->getUser();
	run( 'cd {{release_path}}/theme && {{env_vars}} {{bin/composer}} {{composer_options}}' );
	run( "mkdir -p {{release_path}}/theme/assets" );
	runLocally( "rsync {{rsync_options}} {{local_path}}/theme/assets/ $user@{{server.host}}:{{release_path}}/theme/assets", 600 );
	run( "mv -f {{release_path}}/theme {{release_path}}/public/app/themes/{{application}}" );
});
