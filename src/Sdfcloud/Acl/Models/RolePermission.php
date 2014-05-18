<?php

namespace Sdfcloud\Acl\Models;


/**
 * Eloquent model for roles_permissions table.
 * RolePermission will be used by Eloquent permissions provider.
 */

class RolePermission extends \Illuminate\Database\Eloquent\Model{
    protected $table = 'roles_permissions';

    protected $fillable = array('permission_id', 'role_id', 'allowed', 'allowed_ids', 'excluded_ids', 'created_at', 'updated_at');

    public $timestamps = true;
}
