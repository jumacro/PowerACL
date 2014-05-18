<?php

namespace Sdfcloud\Acl\Commands;

use Illuminate\Console\Command;

use Sdfcloud\Acl\Acl;

/**
 * ResetCommand Class
 * 
 * Custom Artisan command for reseting ACL permissions.
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
class ResetCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'acl:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset all ACL permissions. This will delete both user and system permissions and install permissions from the config file';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire() {
        Acl::reloadPermissions();

        Acl::reloadGroups();

        Acl::reloadRoles();

        $this->info('ACL permissions successfully reseted!');
    }

}
