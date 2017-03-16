<?php

namespace Deployer;

use Deployer\Task\Context;

desc( 'Push local uploads dir to remote' );
task( 'push:uploads', function() {
	if( isVerbose() ) {
		writeln( 'Pushing local uploads to the remote server.' );
	}
	$server = Context::get()->getServer()->getConfiguration();
	$user = $server->getUser();
	$date = date( 'Y-m-d H:i' );
	run( 'if [ ! -d {{deploy_path}}/{{uploads_remote_path}} ]; then mkdir -p {{deploy_path}}/{{uploads_remote_path}}; fi' );
	runLocally( "rsync {{rsync_options}} {{local_path}}/{{uploads_local_path}}/ $user@{{server.host}}:{{deploy_path}}/{{uploads_remote_path}}/", 1200 );
	run( "echo '$date, Synced uploads from local' >> {{deploy_path}}/.dep/revision.log" );
});

desc( 'Export the local database to remote' );
task( 'push:database', function() {
	$release = get( 'release_name' );
	if( intval( $release ) > 1 ) {
		$release -= 1;
		if( isVerbose() ) {
			writeln( 'Pushing local database to the remote server.' );
		}
		$date = date( 'Y-m-d H:i' );
		cd( '{{deploy_path}}' );
		runLocally( 'wp db export - --path={{wp_cli_path}} | gzip > wpcli_database.sql.gz' );
		upload( 'wpcli_database.sql.gz', get( 'deploy_path' ) . '/wpcli_database.sql.gz' );
		run( "gunzip < wpcli_database.sql.gz | {{deploy_path}}/releases/$release/vendor/bin/wp db import - --path={{deploy_path}}/{{wp_cli_path}}" );
		run( "{{deploy_path}}/releases/$release/vendor/bin/wp search-replace ://{{local_url}} ://{{server.host}} --skip-columns=guid --path={{deploy_path}}/{{wp_cli_path}}" );
		run( "echo '$date, Imported database' >> .dep/revision.log" );
	}
});

desc( 'Perform cleanup and flush remote permalinks' );
task( 'push:cleanup', function() {
	$release = get( 'release_name' );
	if( intval( $release ) > 1 ) {
		$release -= 1;
		if( isVerbose() ) {
			writeln( 'Cleaning up local machine.' );
		}
		run( 'rm {{deploy_path}}/wpcli_database.sql.gz' );
		runLocally( 'rm wpcli_database.sql.gz' );
		if( isVerbose() ) {
			writeln( 'Rewrite/flush remote permalinks.' );
		}
		run( "{{deploy_path}}/releases/$release/vendor/bin/wp rewrite flush --path={{deploy_path}}/{{wp_cli_path}}" );
	}
});
