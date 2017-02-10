<?php

/**
 * Rollback to previous release.
 */
task( 'rollback', function()
{
	$releases = env( 'releases_list' );
	$server   = \Deployer\Task\Context::get()->getServer()->getConfiguration();
	$user     = $server->getUser();

	if( isset( $releases[1] ) ) {

		$releaseDir = "{{deploy_path}}/releases/{$releases[1]}";
		$timestamp  = date( 'Y-m-d H:i' );

		// Symlink to old release.
		run( "cd {{deploy_path}} && ln -nfs $releaseDir {{deploy_path}}/{{public_dir}}" );

		// Remove release
		run( "rm -rf {{deploy_path}}/releases/{$releases[0]}" );

		// Log rollback
		run( "echo \"$timestamp - $user rolled back to release {$releases[1]}\" >> {{deploy_path}}/revision.log" );

		if( isVerbose() ) {
			writeln( "Rollback to `{$releases[1]}` release was successful." );
		}
	}
	else {
		writeln( "<comment>No more releases you can revert to.</comment>" );
	}
})->desc( 'Rollback to previous release' );
