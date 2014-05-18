<?php

return array(

    /*
    |--------------------------------------------------------------------------
    | Default Permission Provider
    |--------------------------------------------------------------------------
    |
    | This option controls what provider will ACL use.
    | Currently there is only one provider "eloquent".
    |
    | Supported: "eloquent"
    |
    */
    'provider' => 'eloquent',

    /*
    |--------------------------------------------------------------------------
    | Super users array
    |--------------------------------------------------------------------------
    |
    | Put here user IDs that will have superuser rights.
    |
    */
    'superusers' => array(),

    /*
    |--------------------------------------------------------------------------
    | Guest users ID
    |--------------------------------------------------------------------------
    |
    | Put here ID that will used for setting permissions to guest users.
    |
    */
    'guestuser' => 0,

    /*
    |--------------------------------------------------------------------------
    | Permissions in the application
    |--------------------------------------------------------------------------
    |
    | This option needs to contain all system wide permissions.
    |
    */
    'permissions' => array(),

    /*
    |--------------------------------------------------------------------------
    | Permission groups
    |--------------------------------------------------------------------------
    |
    | Every permission can belong to some group. You can have groups that
    | belongs to other group. Every group can have a route.
    |
    |,
    */
    'groups' => array(),

    /*
    |--------------------------------------------------------------------------
    | Roles
    |--------------------------------------------------------------------------
    |
    | Roles can have set of permissions as well as parent and children roles.
    | To use roles add roles column to your users table.
    |
    |
   */
    'roles' => array(
            array(
                'id' => '1',
                'name' => 'Super User',
            ),
            array(
                'id' => '2',
                'name' => 'Reseller',
            ),
            array(
                'id' => '3',
                'name' => 'End User',
            )
       ),

);
