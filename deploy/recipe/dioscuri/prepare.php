<?php

/**
 * Replaces current with {{current_dir}}
 */

namespace Deployer;

use Deployer\Task\Context;
use function Deployer\Support\str_contains;

desc('Preparing host for deploy');
task('deploy:prepare', function () {
    // Check if shell is POSIX-compliant
    $result = run('echo $0');

    if (!str_contains($result, 'bash') && !str_contains($result, 'sh')) {
        throw new \RuntimeException(
            'Shell on your server is not POSIX-compliant. Please change to sh, bash or similar.'
        );
    }

    run('if [ ! -d {{deploy_path}} ]; then mkdir -p {{deploy_path}}; fi');

    // Check for existing /{{current_dir}} directory (not symlink)
    $result = test('[ ! -L {{deploy_path}}/{{current_dir}} ] && [ -d {{deploy_path}}/{{current_dir}} ]');
    if ($result) {
        throw new \RuntimeException('There already is a directory (not symlink) named "{{current_dir}}" in ' . get('deploy_path') . '. Remove this directory so it can be replaced with a symlink for atomic deployments.');
    }

    // Create metadata .dep dir.
    run("cd {{deploy_path}} && if [ ! -d .dep ]; then mkdir .dep; fi");

    // Create releases dir.
    run("cd {{deploy_path}} && if [ ! -d releases ]; then mkdir releases; fi");

    // Create shared dir.
    run("cd {{deploy_path}} && if [ ! -d shared ]; then mkdir shared; fi");
});
