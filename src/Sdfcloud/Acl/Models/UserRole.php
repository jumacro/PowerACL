<?php

namespace Sdfcloud\Acl\Models;

/**
 * Eloquent model for users_roles table.
 * UserRole will be used by Eloquent permissions provider.
 */
class UserRole extends \Illuminate\Database\Eloquent\Model {

    protected $table = 'users_roles';
    protected $fillable = array('user_id', 'role_id');
    public $timestamps = false;

}
