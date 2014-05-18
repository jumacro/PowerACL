<?php

namespace Sdfcloud\Acl;

use Illuminate\Support\Facades\Config;

class Manager {

    private $provider;
    private $allPermissions = array();
    private $cached = array();

    public function __construct(PermissionsProviderAbstract $provider) {
        $this->provider = $provider;

        // set system default permissions
        $this->allPermissions = $this->provider->getAllPermissions();
    }
    
    
    
    /**
     * Reload system permission from config file
     *
     * @param boolean $onlySystemPermissions
     *
     * @return array
     */
    public function reloadPermissions($onlySystemPermissions = false) {
        $permissions = Config::get('acl::permissions');
        $forDelete = array();

        if ($onlySystemPermissions) {
            // delete not existing permissions from users_permissions
            // get old permissions
            $old = $this->provider->getAllPermissions();
            foreach ($old as $oldPermission) {
                $exist = false;
                foreach ($permissions as $newPermissions) {
                    $exist = $newPermissions['id'] == $oldPermission['id'];

                    if ($exist) {
                        break;
                    }
                }

                if (!$exist) {
                    // delete only user permissions that not exist anymore
                    $forDelete[] = $oldPermission['id'];
                }
            }

            foreach ($forDelete as $id) {
                $this->removeUserPermission(null, $id);
            }
        } else {
            $this->deleteAllUsersPermissions();
        }

        $this->deleteAllPermissions();

        foreach ($permissions as $permission) {
            $this->createPermission(
                $permission['id'], $permission['allowed'], $permission['module'], $permission['route'], $permission['resource_id_required'], $permission['name'], @$permission['group_id']
            );
        }

        return $forDelete;
    }

    /**
     * Reload roles from config file into DB
     *
     * @param string $parentRole
     * @param array $roles
     *
     * @return type
     */
    public function reloadRoles($parentRole = null, $roles = null) {
        if (empty($roles)) {
            $roles = Config::get('acl::roles');
        }

        if ($parentRole === null) {
            $this->deleteAllRoles();
        }

        $newRoles = array();

        foreach ($roles as $role) {
            if (empty($role['children'])) {
                $newRoles[$role['id']] = $parentRole;
                $this->insertRole($role['id'], $role['name'], $parentRole);
            } else {
                $newRoles[$role['id']] = $parentRole;
                $this->insertRole($role['id'], $role['name'], $parentRole);
                $newRoles = array_merge(
                    $newRoles, $this->reloadRoles($role['id'], $role['children'])
                );
            }
        }

        return $newRoles;
    }

    /**
     * Reload groups from config file into DB
     *
     * @param string $parentGroup
     * @param array $groups
     *
     * @return type
     */
    public function reloadGroups($parentGroup = null, $groups = null) {
        if (empty($groups)) {
            $groups = Config::get('acl::groups');
        }

        if ($parentGroup === null) {
            $this->deleteAllGroups();
        }

        $newGroups = array();

        foreach ($groups as $group) {
            if (empty($group['children'])) {
                $newGroups[$group['id']] = $parentGroup;
                $this->insertGroup($group['id'], $group['name'], @$group['route'], $parentGroup);
            } else {
                $newGroups[$group['id']] = $parentGroup;
                $this->insertGroup($group['id'], $group['name'], @$group['route'], $parentGroup);
                $newGroups = array_merge(
                    $newGroups, $this->reloadGroups($group['id'], $group['children'])
                );
            }
        }

        return $newGroups;
    }

    /**
     * Create new permission
     *
     * @param integer $id
     * @param boolean $allowed
     * @param string $module 
     * @param string|array $route
     * @param boolean $resourceIdRequired
     * @param string $name
     * @param integer $groupId
     */
    public function createPermission($id, $allowed, $module, $route, $resourceIdRequired, $name, $groupId = null) {
        return $this->provider->createPermission($id, $allowed, $module, $route, $resourceIdRequired, $name, $groupId);
    }

    /**
     * Remove permission
     *
     * @param string $id
     */
    public function removePermission($id) {
        return $this->provider->removePermission($id);
    }

    /**
     * Assign permission to the specific user.
     *
     * @param integer $userId
     * @param integer $permissionId
     * @param boolean $allowed
     * @param array $allowedIds
     * @param array $excludedIds
     */
    public function assignPermission(
    $userId, $permissionId, $allowed = null, array $allowedIds = null, array $excludedIds = null
    ) {
        return $this->provider->assignPermission($userId, $permissionId, $allowed, $allowedIds, $excludedIds);
    }

    /**
     * Remove permission from the user.
     *
     * @param integer $userId
     * @param integer $permissionId
     */
    public function removeUserPermission($userId, $permissionId) {
        return $this->provider->removeUserPermission($userId, $permissionId);
    }

    /**
     * Remove all user's permissions
     *
     * @param integer $userId
     */
    public function removeUserPermissions($userId) {
        return $this->provider->removeUserPermissions($userId);
    }

    /**
     * Update user permission (only if that user permission exist).
     *
     * @param integer $userId
     * @param integer $permissionId
     * @param boolean $allowed
     * @param array $allowedIds
     * @param array $excludedIds
     */
    public function updateUserPermission(
    $userId, $permissionId, $allowed = null, array $allowedIds = null, array $excludedIds = null
    ) {
        return $this->provider->updateUserPermission($userId, $permissionId, $allowed, $allowedIds, $excludedIds);
    }

    /**
     * Get specific user permission
     *
     * @param integer $userId
     * @param integer $permissionId
     *
     * @return array
     */
    public function getUserPermission($userId, $permissionId) {
        return $this->provider->getUserPermission($userId, $permissionId);
    }

    /**
     * Set user permission. If permission exist update, otherwise create.
     *
     * @param integer $userId
     * @param integer $permissionId
     * @param boolean $allowed
     * @param array $allowedIds
     * @param array $excludedIds
     */
    public function setUserPermission($userId, $permissionId, $allowed = null, array $allowedIds = null, array $excludedIds = null) {
        $permission = $this->getUserPermission($userId, $permissionId);
        if (empty($permission)) {
            return $this->provider->assignPermission($userId, $permissionId, $allowed, $allowedIds, $excludedIds);
        } else {
            return $this->provider->updateUserPermission($userId, $permissionId, $allowed, $allowedIds, $excludedIds);
        }
    }

    /**
     * Get all permissions.
     *
     * @return array
     */
    public function getAllPermissions() {
        return $this->allPermissions;
    }

    /**
     * Get user permissions
     *
     * @param integer $userId
     *
     * @return array
     */
    public function getUserPermissions($userId) {
        if (!isset($this->cached[$userId])) {

            // get user permissions
            $userPermissions = $this->provider->getUserPermissions($userId);

            // get user permissions from user roles
            $userPermissionsBasedOnRoles = $this->provider->getUserPermissionsBasedOnRoles($userId);

            $permissions = array();

            // get all permissions
            foreach ($this->allPermissions as $permission) {
                $permission['allowed_ids'] = null;
                $permission['excluded_ids'] = null;
                unset($permission['name']);

                $permissions[$permission['id']] = $permission;
            }

            // overwrite system permissions with user permissions from roles
            foreach ($userPermissionsBasedOnRoles as $userRolePermission) {
                if (@$userRolePermission['allowed'] === null) {
                    // allowed is not set, so use from system default
                    unset($userRolePermission['allowed']);
                }

                $temp = $permissions[$userRolePermission['id']];

                $temp = array_merge($temp, $userRolePermission);

                $permissions[$userRolePermission['id']] = $temp;
            }

            // overwrite system permissions and user permissions from roles with user permissions
            foreach ($userPermissions as $userPermission) {
                if (@$userPermission['allowed'] === null) {
                    // allowed is not set, so use from system default
                    unset($userPermission['allowed']);
                }

                $temp = $permissions[$userPermission['id']];

                $temp = array_merge($temp, $userPermission);

                $permissions[$userPermission['id']] = $temp;
            }

            // set finall permissions for particular user
            $this->cached[$userId] = $permissions;
        }

        return $this->cached[$userId];
    }

    /**
     * Update user permissions
     *
     * @param integer $userId
     * @param array $permissions
     */
    public function updateUserPermissions($userId, array $permissions) {
        foreach ($permissions as $permission) {
            $this->updateUserPermission(
                $userId, $permission['id'], @$permission['allowed'], @$permission['allowed_ids'], @$permission['excluded_ids']
            );
        }
    }

    /**
     * Delete all permissions
     */
    public function deleteAllPermissions() {
        return $this->provider->deleteAllPermissions();
    }

    /**
     * Delete all users permissions
     */
    public function deleteAllUsersPermissions() {
        return $this->provider->deleteAllUsersPermissions();
    }

    /**
     * Get all permission placed into groups as children nodes.
     *
     * @param array $grouped
     * @param array $groups
     *
     * @return array
     */
    public function getAllPermissionsGrouped($grouped = null, $groups = null) {
        $permissions = null;

        if ($grouped === null) {
            $permissions = $this->provider->getAllPermissions();

            $grouped = array();
            foreach ($permissions as $key => $permission) {
                if (isset($permission['group_id'])) {
                    $grouped[$permission['group_id']][] = $permission;
                    unset($permissions[$key]);
                }
            }
        }

        if ($groups === null) {
            $groups = Config::get('acl::groups');
        }

        foreach ($groups as &$group) {
            if (!empty($group['children'])) {
                $temp = $this->getAllPermissionsGrouped($grouped, $group['children']);

                $group['children'] = $temp;

                if (!empty($grouped[$group['id']])) {
                    if (!isset($group['children'])) {
                        $group['children'] = array();
                    }
                    $group['children'] = array_merge($group['children'], $grouped[$group['id']]);
                }
            } else {
                if (!empty($grouped[$group['id']])) {
                    if (!isset($group['children'])) {
                        $group['children'] = array();
                    }
                    $group['children'] = array_merge($group['children'], $grouped[$group['id']]);
                }
            }
        }

        if ($permissions !== null) {
            return array_merge($groups, $permissions);
        }

        return $groups;
    }

    /**
     * Insert new group
     *
     * @param integer $id
     * @param string $name
     * @param array|string $route
     * @param integer $parentId
     *
     * @return type
     */
    public function insertGroup($id, $name, $route = null, $parentId = null) {
        return $this->provider->insertGroup($id, $name, $route, $parentId);
    }

    /**
     * Get all groups
     */
    public function getGroups() {
        return $this->provider->getGroups();
    }

    /**
     * Delete all groups.
     */
    public function deleteAllGroups() {
        return $this->provider->deleteAllGroups();
    }

    /**
     * Get all children of a group
     *
     * @param int $id
     * @param boolean $selfinclude
     * @param boolean $recursive
     * @return array List of children groups
     */
    public function getChildGroups($id, $selfinclude = true, $recursive = true) {
        $groups = $this->getGroups();

        $childs = array();
        foreach ($groups as $group) {
            if ($group['parent_id'] == $id || ($selfinclude && $group['id'] == $id)) {
                $childs[$group['id']] = $group;

                if ($recursive && $group['id'] != $id) {
                    $childs = array_merge($childs, $this->getChildGroups($group['id']));
                }
            }
        }

        return $childs;
    }

    /**
     * Insert new role
     *
     * @param integer $id
     * @param string $name
     * @param array|string $permissionIds
     * @param integer $parentId
     *
     * @return type
     */
    public function insertRole($id, $name, $parentId = null) {
        return $this->provider->insertRole($id, $name, $parentId);
    }

    /**
     * Delete all roles
     */
    public function deleteAllRoles() {
        return $this->provider->deleteAllRoles();
    }

    /**
     * Get user roles
     *
     * @param integer $userId
     *
     * @return array
     */
    public function getUserRoles($userId) {
        return $this->provider->getUserRoles($userId);
    }

}
