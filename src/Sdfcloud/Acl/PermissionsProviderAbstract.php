<?php

namespace Sdfcloud\Acl;

abstract class PermissionsProviderAbstract {

    /**
     * Will return array of user permissions 
     * 
     * 
     * @param integer $userId
     *
     * @return array
     */
    public abstract function getUserPermissions($userId);

    /**
     * Get all permissions a role has.
     * Will return array of role permissions
     *
     * @param string $roleId
     *
     * @return array
     */
    public abstract function getRolePermissions($roleId);

    /**
     * Get all permissions user has based on assigned roles
     * Will return array of role permissions
     *
     * @param string $userId
     *
     * @return array
     */
    public abstract function getUserPermissionsBasedOnRoles($userId);

    /**
     * Will return array of all system permissions
     *
     * @return array
     */
    public abstract function getAllPermissions();

    /**
     * Delete all user permissions
     */
    public abstract function deleteAllUsersPermissions();

    /**
     * Delete all  permissions
     */
    public abstract function deleteAllPermissions();

    /**
     * Crate new permission
     *
     * @param string $id
     * @param bool $allowed
     * @param string $module 
     * @param string|array $route
     * @param bool $resourceIdRequired
     * @param string $name
     * @param integer $groupId
     *
     * @return array
     */
    public abstract function createPermission($id, $allowed, $module, $route, $resourceIdRequired, $name, $groupId = null);

    /**
     * Remove permission by ID
     *
     * @param string $id
     */
    public abstract function removePermission($id);

    /**
     * Assign permission to the user with specfic options
     *
     * @param integer $userId
     * @param string $permissionId
     * @param boolean $allowed
     * @param array $allowedIds
     * @param array $excludedIds
     */
    public abstract function assignPermission(
    $userId, $permissionId, $allowed = null, array $allowedIds = null, array $excludedIds = null
    );

    /**
     * Remove specific user permission.
     * If $userId can be null.
     *
     * @param integer $userId
     * @param string $permissionId
     */
    public abstract function removeUserPermission($userId, $permissionId);

    
    /**
     * Remove all user's permissions.
     *
     * @param integer $userId
     */
    public abstract function removeUserPermissions($userId);

    
    /**
     * Update specific user permission
     *
     * @param integer $userId
     * @param string $permissionId
     * @param bool $allowed
     * @param array $allowedIds
     * @param array $excludedIds
     */
    public abstract function updateUserPermission(
    $userId, $permissionId, $allowed = null, array $allowedIds = null, array $excludedIds = null
    );

    /**
     * return all groups 
     */
    public abstract function getGroups();

    
    /**
     * Insert new group
     *
     * @param string $id
     * @param string $name
     * @param array|string $route
     * @param type $parentId
     *
     */
    public abstract function insertGroup($id, $name, $route = null, $parentId = null);

    
    /**
     * Insert new role
     *
     * @param string $id
     * @param string $name
     * @param array|string $permissionIds
     * @param type $parentId
     *
     */
    public abstract function insertRole($id, $name, $parentId = null);

    /**
     * Delete all groups.
     */
    public abstract function deleteAllGroups();

    
    /**
     * Get user roles
     *
     * @param integer $userId
     *
     * @return array
     */
    public abstract function getUserRoles($userId);

    
    
    /**
     * Get specific user permission
     *
     * @param integer $userId
     * @param string $permissionId
     *
     * @return array
     */
    public abstract function getUserPermission($userId, $permissionId);

    /**
     * Delete all roles.
     */
    public abstract function deleteAllRoles();
}
