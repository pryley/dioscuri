<?php

/**
 * Rsync the uploads dir
 */
task( 'push:uploads', function()
{
	if( isVerbose() ) {
		writeln( 'Pushing local uploads to the remote server.' );
	}

	$server = \Deployer\Task\Context::get()->getServer()->getConfiguration();
	$user   = $server->getUser();

	runLocally( "rsync {{rsync_options}} {{local_uploads_dir}}/ $user@{{server.host}}:{{deploy_path}}/{{uploads_dir}}/", 600 );
})->desc( 'Push local uploads' );

/**
 * Push the local database
 */
task( 'push:database', function()
{
	if( isVerbose() ) {
		writeln( 'Pushing local database to the remote server.' );
	}

	$releases = env( 'releases_list' );

	cd( '{{deploy_path}}' );

	runLocally( 'wp db export - --path=public | gzip > wpcli_database.sql.gz' );

	upload( 'wpcli_database.sql.gz', env( 'deploy_path' ) . '/wpcli_database.sql.gz' );

	run( "gunzip < wpcli_database.sql.gz | {{deploy_path}}/releases/{$releases[0]}/vendor/bin/wp db import - --path={{deploy_path}}/{{public_dir}}" );
	run( "{{deploy_path}}/releases/{$releases[0]}/vendor/bin/wp search-replace ://{{local_url}} ://{{server.host}} --skip-columns=guid --path={{deploy_path}}/{{public_dir}}" );
})->desc( 'Push local database' );

/**
 * Cleanup
 */
task( 'push:cleanup', function()
{
	if( isVerbose() ) {
		writeln( 'Cleaning up local machine.' );
	}

	run( 'rm {{deploy_path}}/wpcli_database.sql.gz' );
	runLocally( 'rm wpcli_database.sql.gz' );

	if( isVerbose() ) {
		writeln( 'Rewrite/flush remote permalinks.' );
	}

	// Log push
	$timestamp = date( 'Y-m-d H:i' );

	run( "echo '$timestamp - Pushed local database and uploads.' >> {{deploy_path}}/revision.log" );

	$releases = env( 'releases_list' );

	run( "{{deploy_path}}/releases/{$releases[0]}/vendor/bin/wp rewrite flush --path={{deploy_path}}/{{public_dir}}" );
})->desc( 'Cleanup and flush remote permalinks' );
