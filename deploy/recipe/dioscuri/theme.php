<?php

namespace Deployer;

desc( 'Installing theme vendors' );
task( 'deploy:theme_vendors', function() {
	run( 'cd {{release_path}}/theme && {{bin/composer}} {{composer_options}}' );
});

desc( 'Deploy theme' );
task( 'deploy:theme', function() {
	$rsync_options = ['options' => get( 'rsync_options' )];
	run( "mkdir -p {{release_path}}/theme/assets" );
	upload( './theme/assets/', '{{release_path}}/theme/assets', $rsync_options );
	upload( './pollux.yml', '{{release_path}}/pollux.yml', $rsync_options );
	run( "mv -f {{release_path}}/theme {{release_path}}/public/app/themes/{{application}}" );
});

desc( 'Deploy only the theme' );
task( 'theme', function() {
	$rsync_options = ['options' => get( 'rsync_options' )];
	upload( './theme/', '{{release_path}}/public/app/themes/{{application}}', $rsync_options );
	upload( './pollux.yml', '{{release_path}}/pollux.yml', $rsync_options );
});
