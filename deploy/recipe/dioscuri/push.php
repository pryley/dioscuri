<?php

namespace Deployer;

require_once 'env.php';

task( 'push:info', function() {
	$stage = get( 'stage' );
	writeln( "✈︎ Pushing <fg=cyan>development</fg=cyan> to <fg=magenta>{$stage}</fg=magenta>" );
})->shallow()->setPrivate();

desc( 'Push local uploads dir to remote' );
task( 'push:uploads', function() {
	if( isVerbose() ) {
		writeln( 'Pushing local uploads to the remote server.' );
	}
	$rsync_options = ['options' => get( 'rsync_options' )];
	run( 'if [ ! -d {{deploy_path}}/{{uploads_remote_path}} ]; then mkdir -p {{deploy_path}}/{{uploads_remote_path}}; fi' );
	upload( '{{local_path}}/{{uploads_local_path}}/', '{{deploy_path}}/{{uploads_remote_path}}/', $rsync_options );
});

desc( 'Export the local database to remote' );
task( 'push:database', function() {
	if( empty( get( 'release_name' )))return;
	$environments = unserialize( ENVIRONMENTS );
	$localhost = preg_quote( $environments['development'], '/' );
	$stage = get( 'stage' );
	if( empty( $environments[$stage] )) {
		writeln( "Stage ($stage) not found in ENVIRONMENTS constant." );
		return;
	}
	if( isVerbose() ) {
		writeln( 'Pushing local database to the remote server.' );
	}
	cd( '{{deploy_path}}' );
	runLocally( 'wp db export - --path={{wp_cli_path}} | gzip > wpcli_database.sql.gz' );
	upload( 'wpcli_database.sql.gz', '{{deploy_path}}/wpcli_database.sql.gz' );
	run( "gunzip < wpcli_database.sql.gz | {{release_path}}/vendor/bin/wp db import - --path={{release_path}}/{{wp_cli_path}}" );
	run( "{{release_path}}/vendor/bin/wp search-replace '{$localhost}|https?:\/\/(localhost(:[0-9]{4})?)' '$environments[$stage]' --regex --skip-columns=guid --path={{release_path}}/{{wp_cli_path}}" );
});

desc( 'Perform cleanup and flush remote permalinks' );
task( 'push:cleanup', function() {
	if( empty( get( 'release_name' )))return;
	if( isVerbose() ) {
		writeln( 'Cleaning up local machine.' );
	}
	run( 'rm {{deploy_path}}/wpcli_database.sql.gz' );
	runLocally( 'rm wpcli_database.sql.gz' );
	if( isVerbose() ) {
		writeln( 'Rewrite/flush remote permalinks.' );
	}
	run( "{{release_path}}/vendor/bin/wp rewrite flush --path={{release_path}}/{{wp_cli_path}}" );
});
