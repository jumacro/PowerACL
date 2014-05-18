<?php
namespace Sdfcloud\Acl\Models;


/**
 * Eloquent model for roles table.
 * Role will be used by Eloquent permissions provider.
 */
class Role extends \Illuminate\Database\Eloquent\Model{
    protected $table = 'roles';

    protected $fillable = array('id', 'name', 'permission_ids', 'parent_id', 'created_at', 'updated_at');

    public $timestamps = true;
}
