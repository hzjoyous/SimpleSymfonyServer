<?php
namespace Deployer;

require 'recipe/symfony4.php';

// Project name
set('application', 'api.nonodi.com');

// Project repository
set('repository', 'https://github.com/hzjoyous/api.nonodi.com.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', false);

// Shared files/dirs between deploys 
set('shared_files', [
    '.env.local'
]);
add('shared_dirs', []);

// Writable dirs by web server 
add('writable_dirs', []);
set('allow_anonymous_stats', false);

// Hosts

// 开发环境

host('111.231.202.11')
    ->stage('prod')
    // ->set('branch', 'production')
    ->user('www')
    ->set('deploy_path', '/home/www/{{application}}');


task('opcache_clear', function() {
    run('cd {{current_path}} && composer dump-env prod');
    run('curl "http://api.nonodi.com/opclean"');
});

// Tasks

task('build', function () {
    run('cd {{release_path}} && build');
});

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');
after('deploy:symlink', 'opcache_clear');
// Migrate database before symlink new release.
//database:migrate 暂不需要
//before('deploy:symlink', 'database:migrate');

 
