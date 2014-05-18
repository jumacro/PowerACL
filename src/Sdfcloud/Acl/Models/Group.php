<?php

namespace Sdfcloud\Acl\Models;


/**
 * Eloquent model for groups table.
 * Group will be used by Eloquent permissions provider.
 */
class Group extends \Illuminate\Database\Eloquent\Model{
    protected $table = 'groups';
    
    protected $fillable = array('id', 'name', 'route', 'parent_id', 'created_at', 'updated_at');

    public $timestamps = true;
}
