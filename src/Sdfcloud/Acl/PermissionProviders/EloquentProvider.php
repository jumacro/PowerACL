<?php

namespace Sdfcloud\Acl\PermissionProviders;

use Sdfcloud\Acl\Models\Group;
use Sdfcloud\Acl\Models\Role;
use Sdfcloud\Acl\Models\Permission;
use Sdfcloud\Acl\Models\RolePermission;
use Sdfcloud\Acl\Models\UserRole;
use Sdfcloud\Acl\Models\UserPermission;

class EloquentProvider extends \Sdfcloud\Acl\PermissionsProviderAbstract {

    /**
     * * Assign permission to the user with specfic options
     *
     * @param integer $userId
     * @param string $permissionId
     * @param boolean $allowed
     * @param array $allowedIds
     * @param array $excludedIds
     * 
     * @return array
     */
    public function assignPermission($userId, $permissionId, $allowed = null, array $allowedIds = null, array $excludedIds = null) {
        return UserPermission::create(array(
                'permission_id' => $permissionId,
                'user_id' => $userId,
                'allowed' => $allowed,
                'allowed_ids' => (!empty($allowedIds)) ? implode(',', $allowedIds) : null,
                'excluded_ids' => (!empty($excludedIds)) ? implode(',', $excludedIds) : null,
            ))->toArray();
    }

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
    public function createPermission($id, $allowed, $module, $route, $resourceIdRequired, $name, $groupId = null) {
        return Permission::create(array(
                'id' => $id,
                'allowed' => $allowed,
                'module' => is_array($module) ? json_encode($module) : $module,
                'route' => is_array($route) ? json_encode($route) : $route,
                'resource_id_required' => $resourceIdRequired,
                'name' => $name,
                'group_id' => $groupId
            ))->toArray();
    }

    /**
     * Delete all groups
     * 
     * @return boolean
     */
    public function deleteAllGroups() {
        return Group::truncate();
    }

    /**
     * Delete all permissions.
     * 
     * @return boolean
     */
    public function deleteAllPermissions() {
        return Permission::truncate();
    }

    /**
     * Delete all roles
     * 
     * @return boolean .
     */
    public function deleteAllRoles() {
        return Role::truncate();
    }

    /**
     * Delete all users permission
     * 
     * @return boolean 
     */
    public function deleteAllUsersPermissions() {
        return UserPermission::truncate();
    }

    /**
     * Get all permissions
     * 
     * @return array
     */
    public function getAllPermissions() {
        $permissions = Permission::all()->toArray();

        foreach ($permissions as &$permission) {
            $routes = json_decode($permission['route'], true);
            if ($routes !== null) {
                // if route is json encoded string
                $permission['route'] = $routes;
            }

            $permission['allowed'] = (bool) $permission['allowed'];
            $permission['resource_id_required'] = (bool) $permission['resource_id_required'];
        }

        return $permissions;
    }

    /**
     * Get all groups
     * 
     * @return array
     */
    public function getGroups() {
        $groups = Group::all()->toArray();

        foreach ($groups as &$group) {
            $routes = json_decode($group['route'], true);
            if ($routes !== null) {
                // if route is json encoded string
                $group['route'] = $routes;
            }
        }

        return $groups;
    }

    /**
     * Get specific role's permissions
     * 
     * @param integer $roleId
     * @return array
     */
    public function getRolePermissions($roleId) {
        $rolePermissions = RolePermission::where('role_id', '=', $roleId)->get()->toArray();

        foreach ($rolePermissions as &$permission) {
            $permission = $this->parseUserOrRolePermission($permission);
        }

        return $rolePermissions;
    }

    /**
     * Will return array of user permissions
     * 
     * @param integer $userId
     * @param integer $permissionId
     * 
     * @return array|null array of permissions or null
     */
    public function getUserPermission($userId, $permissionId) {
        if ($userId === null) {
            // if user_id is null then return all user permissions according to permission_id
            $permissions = UserPermission::where('permission_id', '=', $permissionId)->get()->toArray();
            foreach ($permissions as &$permission) {
                $permission = $this->parseUserOrRolePermission($permission);
            }

            return $permissions;
        } else {
            $permission = UserPermission::where('user_id', '=', $userId)
                ->where('permission_id', '=', $permissionId)
                ->first();
            if ($permission) {
                return $this->parseUserOrRolePermission($permission->toArray());
            }
        }
        return null;
    }

    /**
     * get permissions of a specific user
     * 
     * @param integer $userId
     * @return array
     */
    public function getUserPermissions($userId) {
        $userPermissions = UserPermission::where('user_id', '=', $userId)->get()->toArray();

        foreach ($userPermissions as &$permission) {
            $permission = $this->parseUserOrRolePermission($permission);
        }

        return $userPermissions;
    }

    /**
     * Get all permissions user has based on assigned roles
     * Will return array of role permissions
     * 
     * @param integer $userId
     * @return array
     */
    public function getUserPermissionsBasedOnRoles($userId) {
        $userRolePermissions = UserRole::where('user_id', $userId)
                ->leftJoin('roles_permissions', 'users_roles.role_id', '=', 'roles_permissions.role_id')
                ->get(array('roles_permissions.*'))->toArray();

        foreach ($userRolePermissions as &$permission) {
            $permission = $this->parseUserOrRolePermission($permission);
        }

        return $userRolePermissions;
    }

    /**
     * 
     * @param integer $userId
     * @return type
     */
    public function getUserRoles($userId) {
        return UserRole::where('user_id', $userId)->lists('role_id');
    }

    /**
     * 
     * @param integer $id
     * @param String $name
     * @param string|array $route
     * @param integer $parentId
     * @return array
     */
    public function insertGroup($id, $name, $route = null, $parentId = null) {
        return Group::create(array(
                'id' => $id,
                'name' => $name,
                'route' => is_array($route) ? json_encode($route) : $route,
                'parent_id' => $parentId
            ))->toArray();
    }

    /**
     * 
     * @param integer $id
     * @param string $name
     * @param integer $parentId
     * @return array
     */
    public function insertRole($id, $name, $parentId = null) {
        return Role::create(array(
                'id' => $id,
                'name' => $name,
                'parent_id' => $parentId
            ))->toArray();
    }

    /**
     * Delete specific permission
     * 
     * @param integer $id
     * @return boolean
     */
    public function removePermission($id) {
        return Permission::destroy($id);
    }

    /**
     * 
     * @param integer $userId
     * @param integer $permissionId
     * @return boolean
     */
    public function removeUserPermission($userId, $permissionId) {
        $q = UserPermission::where('permission_id', '=', $permissionId);

        if ($userId !== null) {
            $q->where('user_id', '=', $userId);
        }

        return $q->delete();
    }

    /**
     * Remove all user's permissions.
     *
     * @param integer $userId
     * 
     * @return boolean
     */
    public function removeUserPermissions($userId) {
        return UserPermission::where('user_id', '=', $userId)->delete();
    }

    /**
     * Update specific user permission
     *
     * @param integer $userId
     * @param string $permissionId
     * @param bool $allowed
     * @param array $allowedIds
     * @param array $excludedIds
     * @return type
     */
    public function updateUserPermission($userId, $permissionId, $allowed = null, array $allowedIds = null, array $excludedIds = null) {
        return UserPermission::where('user_id', '=', $userId)
                ->where('permission_id', '=', $permissionId)
                ->update(array(
                    'allowed' => $allowed,
                    'allowed_ids' => (!empty($allowedIds)) ? implode(',', $allowedIds) : null,
                    'excluded_ids' => (!empty($excludedIds)) ? implode(',', $excludedIds) : null,
        ));
    }

    /**
     * 
     * @param array $permission
     * @return array
     */
    private function parseUserOrRolePermission(array $permission) {
        if (empty($permission)) {
            return $permission;
        }

        if ($permission['allowed'] === null) {
            // allowed is not set, so use from system default
            unset($permission['allowed']);
        } else {
            $permission['allowed'] = (bool) $permission['allowed'];
        }

        $permission['id'] = $permission['permission_id'];
        unset($permission['permission_id']);

        if ($permission['allowed_ids'] != null) {
            // create array from string - try to explode by ','
            $permission['allowed_ids'] = explode(',', $permission['allowed_ids']);
        }

        if ($permission['excluded_ids'] != null) {
            // create array from string - try to explode by ','
            $permission['excluded_ids'] = explode(',', $permission['excluded_ids']);
        }

        return $permission;
    }

}
