<?php

/**
 * Rsync the uploads dir
 */
task( 'pull:uploads', function()
{
	if( isVerbose() ) {
		writeln( 'Pulling remote uploads to the local machine.' );
	}

	$server = \Deployer\Task\Context::get()->getServer()->getConfiguration();
	$user   = $server->getUser();

	runLocally( "rsync {{rsync_options}} $user@{{server.host}}:{{deploy_path}}/{{uploads_dir}}/ {{local_uploads_dir}}/", 600 );
})->desc( 'Pull remote uploads' );

/**
 * Pull the remote database
 */
task( 'pull:database', function()
{
	if( isVerbose() ) {
		writeln( 'Pulling remote database to the local machine.' );
	}

	$releases = env( 'releases_list' );

	cd( '{{deploy_path}}' );

	run( "{{deploy_path}}/releases/{$releases[0]}/vendor/bin/wp db export - --path={{deploy_path}}/{{public_dir}} | gzip > wpcli_database.sql.gz" );

	download( 'wpcli_database.sql.gz', env( 'deploy_path' ) . '/wpcli_database.sql.gz' );

	runLocally( 'gunzip < wpcli_database.sql.gz | wp db import - --path=public' );
	runLocally( 'wp search-replace ://{{server.host}} ://{{local_url}} --skip-columns=guid --path=public' );
})->desc( 'Pull remote database' );

/**
 * Cleanup
 */
task( 'pull:cleanup', function()
{
	if( isVerbose() ) {
		writeln( 'Cleaning up remote server.' );
	}

	run( 'rm {{deploy_path}}/wpcli_database.sql.gz' );
	runLocally( 'rm wpcli_database.sql.gz' );

	if( isVerbose() ) {
		writeln( 'Rewrite/flush local permalinks.' );
	}

	runLocally( 'wp rewrite flush --path=public' );
})->desc( 'Cleanup and flush local permalinks' );
