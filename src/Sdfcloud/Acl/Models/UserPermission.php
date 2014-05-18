<?php

namespace Sdfcloud\Acl\Models;


/**
 * Eloquent model for users_permissions table.
 * UserPermission will be used by Eloquent permissions provider.
 */
class UserPermission extends \Illuminate\Database\Eloquent\Model{
    protected $table = 'users_permissions';

    protected $fillable = array('permission_id', 'user_id', 'allowed', 'allowed_ids', 'excluded_ids', 'created_at', 'updated_at');

    public $timestamps = true;
}
