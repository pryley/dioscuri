<?php

namespace Deployer;

use Symfony\Component\Yaml\Yaml;

/**
 * @throws \RuntimeException
 * @return void
 */
function configuration( $file ) {
	$configFileContent = Yaml::parse( file_get_contents( $file ));
	if( !is_array( $configFileContent )) {
		throw new \RuntimeException( "Error in parsing " . $file . " file." );
	}
	foreach( $configFileContent as $key => $value ) {
		set( $key, $value );
	}
}

/**
 * Use {{deploy_path}}/{{current_dir}}
 */
set('current_path', function () {
    $link = run("readlink {{deploy_path}}/{{current_dir}}");
    return substr($link, 0, 1) === '/' ? $link : get('deploy_path') . '/' . $link;
});
