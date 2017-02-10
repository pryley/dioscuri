<?php

/**
 * Return current release path.
 */
env( 'current', function()
{
	return run( "readlink {{deploy_path}}/{{public_dir}}" )->toString();
});

/**
 * Custom bins.
 */
env( 'bin/composer', function()
{
	if( commandExist( 'composer' ) ) {
		$composer = run( 'which composer' )->toString();
	}

	if( empty( $composer ) ) {
		run( "cd {{deploy_path}} && curl -sS https://getcomposer.org/installer | {{bin/php}}" );
		$composer = '{{bin/php}} {{deploy_path}}/composer.phar';
	}

	return $composer;
});
