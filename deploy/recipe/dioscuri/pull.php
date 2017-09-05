<?php

namespace Deployer;

require_once 'env.php';

task( 'pull:info', function() {
	$stage = get( 'stage' );
	writeln( "✈︎ Pulling <fg=magenta>{$stage}</fg=magenta> to <fg=cyan>development</fg=cyan>" );
})->shallow()->setPrivate();

desc( 'Pull remote uploads dir to local' );
task( 'pull:uploads', function() {
	if( isVerbose() ) {
		writeln( 'Pulling remote uploads to the local machine.' );
	}
	$rsync_options = ['options' => get( 'rsync_options' )];
	download( '{{deploy_path}}/{{uploads_remote_path}}/', '{{local_path}}/{{uploads_local_path}}/', $rsync_options );
});

desc( 'Import the remote database to local' );
task( 'pull:database', function() {
	if( empty( get( 'release_name' )))return;
	$environments = unserialize( ENVIRONMENTS );
	$stage = get( 'stage' );
	if( empty( $environments[$stage] )) {
		writeln( "Stage ($stage) not found in ENVIRONMENTS constant." );
		return;
	}
	$remotehost = preg_quote( $environments[$stage], '/' );
	if( isVerbose() ) {
		writeln( 'Pulling remote database to the local machine.' );
	}
	cd( '{{deploy_path}}' );
	run( "{{release_path}}/vendor/bin/wp db export - --path={{release_path}}/{{wp_cli_path}} | gzip > wpcli_database.sql.gz" );
	download( '{{deploy_path}}/wpcli_database.sql.gz', 'wpcli_database.sql.gz' );
	runLocally( 'gunzip < wpcli_database.sql.gz | wp db import - --path={{wp_cli_path}}' );
	runLocally( "wp search-replace \"$remotehost\" \"{$environments['development']}\" --regex --skip-columns=guid --path={{wp_cli_path}}" );
});

desc( 'Perform cleanup and flush local permalinks' );
task( 'pull:cleanup', function() {
	if( empty( get( 'release_name' )))return;
	if( isVerbose() ) {
		writeln( 'Cleaning up remote server.' );
	}
	run( 'rm {{deploy_path}}/wpcli_database.sql.gz' );
	runLocally( 'rm wpcli_database.sql.gz' );
	if( isVerbose() ) {
		writeln( 'Rewrite/flush local permalinks.' );
	}
	runLocally( 'wp rewrite flush --path={{wp_cli_path}}' );
});
