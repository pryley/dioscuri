<?php

namespace Deployer;

use Deployer\Task\Context;

desc( 'Perform initial server check' );
task( 'setup:check', function() {
	// test remote shell is POSIX-compliant
	try {
		$result = run( 'echo $0' )->toString();
		if( $result == 'stdin: is not a tty' ) {
			throw new \RuntimeException(
				"Looks like ssh inside another ssh.\n" .
				"Help: http://goo.gl/gsdLt9"
			);
		}
	}
	catch( \RuntimeException $e ) {
		$formatter = Deployer::get()->getHelper( 'formatter' );
		$errorMessage = [
			"Shell on your server is not POSIX-compliant. Please change to sh, bash or similar.",
			"Usually, you can change your shell to bash by running: chsh -s /bin/bash",
		];
		write( $formatter->formatBlock( $errorMessage, 'error', true ));
		throw $e;
	}
	// test repository access
	try {
		$repository = trim( get( 'repository' ));
		run( "git ls-remote --heads $repository" );
	}
	catch( \RuntimeException $e ) {
		$formatter = Deployer::get()->getHelper( 'formatter' );
		$errorMessage = [
			"Could not read from remote repository.",
		];
		write( $formatter->formatBlock( $errorMessage, 'error', true ));
		throw $e;
	}
});

desc( 'Prepare server for deployment' );
task( 'setup:prepare', function() {
	run( 'if [ ! -d {{deploy_path}} ]; then mkdir -p {{deploy_path}}; fi' );
	$result = run( 'if [ ! -L {{deploy_path}}/{{public_dir}} ] && [ -d {{deploy_path}}/{{public_dir}} ]; then echo true; fi' )->toBool();
	if( $result ) {
		// throw new \RuntimeException('There already is a directory (not symlink) named "current" in ' . get('deploy_path') . '. Remove this directory so it can be replaced with a symlink for atomic deployments.');
		run( 'mv {{deploy_path}}/{{public_dir}} {{deploy_path}}/{{public_dir}}.bak' );
		writeln( '<comment>Renamed ' . get('public_dir') . ' to: ' . get('public_dir') . '.bak</comment>' );
	}
	cd( '{{deploy_path}}' );
	run( 'if [ ! -d .dep ]; then mkdir .dep; fi' );
	run( 'if [ ! -f .dep/revision.log ]; then touch .dep/revision.log; fi' );
	run( 'if [ ! -d releases ]; then mkdir releases; fi' );
	run( 'if [ ! -d shared ]; then mkdir shared; fi' );
	// upload stage-specific environment file if needed
	$stage = Context::get()->getServer()->getConfiguration()->getName();
	if( run( "if [ ! -f {{deploy_path}}/env.php ]; then echo 'true'; fi" )->toBool() ) {
		foreach( [sprintf( 'env.%s.php', $stage ), 'env.example.php'] as $file ) {
			if( is_file( $file )) {
				upload( $file, get( 'deploy_path' ) . '/env.php' );
				break;
			}
		}
	}
});

desc( 'Upload shared dirs' );
task( 'setup:shared_dirs', function() {
	// @todo: initial upload shared dirs here
});

desc( 'Upload shared files' );
task( 'setup:shared_files', function() {
	$stage = Context::get()->getServer()->getConfiguration()->getName();
	$dirName = get( 'deploy_path' ) . '/shared/';
	foreach( get( 'shared_files' ) as $file ) {
		$fileInfo = pathinfo( $file );
		if( empty( $fileInfo['filename'] ) && !empty( $fileInfo['extension'] )) {
			$stagingFile = sprintf( '%s.%s', $file, $stage );
		}
		else {
			$stagingFile = sprintf( '%s%s.%s',
				substr( $file, 0, strlen( $file ) - strlen( $fileInfo['extension'] )),
				$stage,
				$fileInfo['extension']
			);
		}
		if( run( "if [ ! -f $(echo $dirName$file) ]; then echo 'true'; fi" )->toBool() ) {
			if( is_file( $stagingFile )) {
				upload( $stagingFile, $dirName . $file );
			}
			else if( is_file( $file )) {
				upload( $file, $dirName . $file );
			}
		}
	}
});

desc( 'Update composer' );
task( 'setup:composer', function() {
	$composer = get( 'bin/composer' );
	run( "$composer self-update; $composer clear-cache" );
});
