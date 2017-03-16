<?php

namespace Deployer;

use Deployer\Task\Context;

desc( 'Pull remote uploads dir to local' );
task( 'pull:uploads', function() {
	if( isVerbose() ) {
		writeln( 'Pulling remote uploads to the local machine.' );
	}
	$server = Context::get()->getServer()->getConfiguration();
    $date = date( 'Y-m-d H:i' );
	runLocally( "rsync {{rsync_options}} {{server.user}}@{{server.host}}:{{deploy_path}}/{{uploads_remote_path}}/ {{local_path}}/{{uploads_local_path}}/", 1200 );
	run( "echo '$date, Pushed uploads to local' >> {{deploy_path}}/.dep/revision.log" );
});

desc( 'Import the remote database to local' );
task( 'pull:database', function() {
	$release = get( 'release_name' );
	if( intval( $release ) > 1 ) {
		$release -= 1;
		if( isVerbose() ) {
			writeln( 'Pulling remote database to the local machine.' );
		}
	    $date = date( 'Y-m-d H:i' );
		cd( '{{deploy_path}}' );
		run( "{{deploy_path}}/releases/$release/vendor/bin/wp db export - --path={{deploy_path}}/{{wp_cli_path}} | gzip > wpcli_database.sql.gz" );
		download( 'wpcli_database.sql.gz', get( 'deploy_path' ) . '/wpcli_database.sql.gz' );
		runLocally( 'gunzip < wpcli_database.sql.gz | wp db import - --path={{wp_cli_path}}' );
		runLocally( 'wp search-replace ://{{server.host}} ://{{local_url}} --skip-columns=guid --path={{wp_cli_path}}' );
		run( "echo '$date, Exported database' >> .dep/revision.log" );
	}
});

desc( 'Perform cleanup and flush local permalinks' );
task( 'pull:cleanup', function() {
	$release = get( 'release_name' );
	if( intval( $release ) > 1 ) {
		if( isVerbose() ) {
			writeln( 'Cleaning up remote server.' );
		}
		run( 'rm {{deploy_path}}/wpcli_database.sql.gz' );
		runLocally( 'rm wpcli_database.sql.gz' );
		if( isVerbose() ) {
			writeln( 'Rewrite/flush local permalinks.' );
		}
		runLocally( 'wp rewrite flush --path={{wp_cli_path}}' );
	}
});
