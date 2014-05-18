<?php

namespace Sdfcloud\Acl\Commands;

use Illuminate\Console\Command;
use Sdfcloud\Acl\Acl;

/**
 * UpdateCommand Class
 * 
 * Custom Artisan command for updating ACL permissions.
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
class UpdateCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'acl:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update all ACL permissions from config file.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire() {
        Acl::reloadPermissions(true);

        Acl::reloadGroups();

        Acl::reloadRoles();

        $this->info('ACL permissions successfully updated!');
    }

}
