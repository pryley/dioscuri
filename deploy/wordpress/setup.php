<?php

/**
 * Check that the repository is reachable.
 */
task( 'setup:check', function()
{
	\Deployer\Task\Context::get()->getServer()->connect();

	// Check if shell is POSIX-compliant
	try {
		cd( '' ); // To run command as raw.
		run( 'echo $0' );
	}
	catch( \RuntimeException $e ) {
		$formatter = \Deployer\Deployer::get()->getHelper( 'formatter' );

		$errorMessage = [
			"Shell on your server is not POSIX-compliant. Please change to sh, bash or similar.",
			"Usually, you can change your shell to bash by running: chsh -s /bin/bash",
		];

		write( $formatter->formatBlock( $errorMessage, 'error', true ) );

		throw $e;
	}

	try {
		$repository = get('repository');
		run( "git ls-remote --heads $repository" );
	} catch (\RuntimeException $e) {

		$formatter = \Deployer\Deployer::get()->getHelper( 'formatter' );

		$errorMessage = [
			"Could not read from remote repository.",
		];

		write( $formatter->formatBlock( $errorMessage, 'error', true ) );

		throw $e;
	}
})->desc( 'Performing initial server checks' );


/**
 * Prepare server for deployment.
 */
task( 'setup:prepare', function()
{
	// Set the deployment timezone
	if( !date_default_timezone_set( env( 'timezone' ) ) ) {
		date_default_timezone_set( 'UTC' );
	}
	// Create deploy dir if needed.
	run( 'if [ ! -d {{deploy_path}} ]; then mkdir -p {{deploy_path}}; fi');

	cd( '{{deploy_path}}' );

	// Create releases dir.
	run( "if [ ! -d releases ]; then mkdir releases; fi" );

	// Create shared dir.
	run( "if [ ! -d shared ]; then mkdir shared; fi" );

	// Initial upload of shared files
	foreach( get( 'shared_files' ) as $file ) {
		if( run( "if [ ! -f $(echo {{deploy_path}}/shared/$file) ]; then echo 'true'; fi")->toBool() ) {
			upload( $file, env( 'deploy_path' ) . '/shared/' . $file );
		}
	}

	// @todo: initial upload shared dirs here
})->desc( 'Preparing server for deploy' );


/**
 * Prepare composer for deployment.
 */
task( 'setup:composer', function()
{
	$composer = env( 'bin/composer' );

	run( "$composer self-update; $composer clear-cache" );
})->desc( 'Preparing composer' );
