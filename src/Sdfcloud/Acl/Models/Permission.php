<?php

namespace Sdfcloud\Acl\Models;


/**
 * Eloquent model for permissions table.
 * Permission will be used by Eloquent permissions provider.
 */
class Permission extends \Illuminate\Database\Eloquent\Model{
    protected $table = 'permissions';

    protected $fillable = array('id', 'allowed', 'module', 'route', 'resource_id_required', 'name', 'group_id', 'created_at', 'updated_at');

    public $timestamps = true;
}
