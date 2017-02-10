<?php

/**
 * Return current release path.
 */
env( 'current', function()
{
	return run( "readlink {{deploy_path}}/{{public_dir}}" )->toString();
});

/**
 * Preparing server for deployment.
 */
task( 'deploy:prepare', function()
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
})->desc( 'Preparing server for deploy' );


// deploy:release
// -> Create new release dir
// -> Link new release dir to /release


/**
 * Update project code
 */
task( 'deploy:git', function()
{
	$at         = '';
	$branch     = env( 'branch' );
	$releases   = env( 'releases_list' );
	$repository = get( 'repository' );
	$server     = \Deployer\Task\Context::get()->getServer()->getConfiguration();
	$user       = $server->getUser();

	if( input()->hasOption( 'tag' ) ) {
		$tag = input()->getOption( 'tag' );
	}

	if( !empty( $tag ) ) {
		$at = "$tag";
	}
	elseif( !empty( $branch ) ) {
		$at = "$branch";
	}
	else {
		$at = 'HEAD';
	}

	// Clone the repo to the cache
	run( "if [ ! -f $(echo {{deploy_path}}/repo/HEAD) ]; then git clone --mirror $repository {{deploy_path}}/repo; fi" );

	// Update the repo mirror to reflect the origin state
	run( "cd {{deploy_path}}/repo; git remote update" );

	// Copy repo to releases
	run( "cd {{deploy_path}}/repo; git archive $at | tar -x -f - -C {{release_path}}" );

	// Set commit var
	$commit = run( "cd {{deploy_path}}/repo; git rev-list --max-count=1 --abbrev-commit $at" );

	// Set log message
	$release = array_shift( $releases );
	set( 'log_message', date( 'Y-m-d H:i' ) . " - Branch $at (at $commit) deployed as release {$release} by $user" );
})->desc( 'Updating code' );


// deploy:vendors
// -> composer install

/**
 * Castor related tasks
 */
task( 'deploy:castor', function()
{
	$application = get( 'application' );

	// Link the app dir
	run( "ln -nfs {{release_path}}/app {{release_path}}/public/app" );

	// Copy the castor-child theme
	run( "cp -R {{release_path}}/theme/public {{release_path}}/public/app/themes/$application" );
})->desc( 'Castor tasks' );


// deploy:release
// -> Create symlinks for shared dirs
// -> Create symlinks for shared files


/**
 * Create symlink to last release.
 */
task( 'deploy:symlink', function()
{
	// backup public dir if it exists as a bona-fide dir.
	run( "if [[ -d $(echo {{deploy_path}}/{{public_dir}}) && ! -L $(echo {{deploy_path}}/{{public_dir}}) ]]; then mv {{deploy_path}}/{{public_dir}} {{deploy_path}}/{{public_dir}}.bak; fi" );

	// Atomic override symlink.
	run( "cd {{deploy_path}} && ln -nfs {{release_path}}/public {{deploy_path}}/{{public_dir}}" );

	// Remove release link.
	run( "cd {{deploy_path}} && rm release" );
})->desc( 'Creating symlinks to release' );


/**
 * Copy directories. Useful for vendors directories
 */
task( 'deploy:copy_dirs', function()
{
	$dirs = get( 'copy_dirs' );

	foreach( $dirs as $dir ) {
		// Delete directory if exists
		run( "if [ -d $(echo {{release_path}}/$dir) ]; then rm -rf {{release_path}}/$dir; fi" );

		// Copy directory
		run( "if [ -d $(echo {{deploy_path}}/{{public_dir}}/$dir) ]; then cp -rpf {{deploy_path}}/{{public_dir}}/$dir {{release_path}}/$dir; fi" );
	}
})->desc( 'Copy directories' );


/**
 * Log revision message.
 */
task( 'deploy:log', function()
{
	$message = get( 'log_message' );

	run( "echo \"$message\" >> {{deploy_path}}/revision.log" );
})->desc( 'Rollback to previous release' );
