<?php

namespace Sdfcloud\Acl\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Database\Schema\Blueprint;
use Schema;

/**
 * InstallCommand Class
 * 
 * Custom Artisan command for installing ACL DB structure.
 * 
 * PHP 5.0 / Laravel 4.0
 * 
 * @author        Mithun Das (mithundas79) on behalf of Pinpoint Media Design (pinpointgraphics)
 * @copyright     Copyright 2014, Pinpoint Media Design
 * @package       Sdfcloud.Acl
 * @property      Acl $Acl
 * @since         SDFCloud 3.0
 * 
 */
class InstallCommand extends Command {

    /**
     * Console command name
     * 
     * @var string 
     */
    protected $name = 'acl:install';

    /**
     * Console command description.
     *
     * @var string
     */
    protected $description = 'Create basic ACL table structure.';

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments() {
        return array(
            array('clean', InputArgument::OPTIONAL, 'Clean install. Delete "permissions" and "users_permissions" table.'),
        );
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire() {
        if ($this->argument('clean')) {
            $this->createConfig();

            //drop table if exists

            if (Schema::hasTable('permissions')) {
                Schema::drop('permissions');
            }
            if (Schema::hasTable('users_permissions')) {
                Schema::drop('users_permissions');
            }
            if (Schema::hasTable('groups')) {
                Schema::drop('groups');
            }
            if (Schema::hasTable('roles_permissions')) {
                Schema::drop('roles_permissions');
            }
            if (Schema::hasTable('roles')) {
                Schema::drop('roles');
            }
            if (Schema::hasTable('users_roles')) {
                Schema::drop('users_roles');
            }
        } elseif (!file_exists(app_path() . '/config/packages/sdfcloud/acl/config.php')) {
            $this->createConfig();
        }

        if (Schema::hasTable('permissions') &&
            Schema::hasTable('users_permissions') &&
            Schema::hasTable('groups') &&
            Schema::hasTable('roles')) {

            $this->error('ACL already installed.');
            return;
        }

        //create tables

        if (!Schema::hasTable('permissions')) {
            Schema::create('permissions', function(Blueprint $table) {
                $table->increments('id');
                $table->boolean('allowed');
                $table->string('module');
                $table->text('route');
                $table->boolean('resource_id_required');
                $table->string('name');
                $table->integer('group_id')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('groups')) {
            Schema::create('groups', function(Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->text('route')->nullable();
                $table->integer('parent_id')->index()->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function(Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->integer('parent_id')->index()->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('users_roles')) {
            Schema::create('users_roles', function(Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id');
                $table->integer('role_id');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('users_permissions')) {
            Schema::create('users_permissions', function(Blueprint $table) {
                $table->increments('id');
                $table->integer('permission_id')->index();
                $table->integer('user_id')->index();
                $table->boolean('allowed')->nullable();
                $table->string('allowed_ids')->nullable();
                $table->string('excluded_ids')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('roles_permissions')) {
            Schema::create('roles_permissions', function(Blueprint $table) {
                $table->increments('id');
                $table->integer('permission_id')->index();
                $table->integer('role_id')->index();
                $table->boolean('allowed')->nullable();
                $table->string('allowed_ids')->nullable();
                $table->string('excluded_ids')->nullable();
                $table->timestamps();
            });
        }
        
        $this->info('ACL has been installed successfully!');
    }

    private function createConfig() {
        return $this->call('config:publish', array('--path' => 'vendor/sdfcloud/acl/src/config', 'package' => 'sdfcloud/acl'));
    }

}
