<?php 

namespace App\Traits;
use App\Models\Role;
use App\Models\Permission;

trait HasRolesAndPermissions
{

    /**
     * @return mixed
     */
    
    public function roles()
    {
        return $this->belongsToMany(Role::class,'users_roles');
    }
    
    /**
     * @return mixed
     */
    
    public function permissions()
    {
        return $this->belongsToMany(Permission::class,'users_permissions');
    }

    /**
     * @param mixed ...$roles
     * @return bool
     *
     * Проверяет есть ли у текущего пользователя необходимая роль
     *
     */

    public function hasRole(... $roles ) {
        foreach ($roles as $role) {
            if ($this->roles->contains('slug', $role)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param Role $role
     * @return bool
     *
     * Назначает текущему пользователю определенную роль
     *
     * $user->roles()->attach( Role::where('slug', 'project-manager')->first() );
     * $user->permissions()->attach( Permission::where('slug','manage-users')->first() );
     */
    public function assignRole(Role $role)
    {
        # можно назначать через attach())
        dd(__METHOD__, $role);
        return $this->roles()->save($role);
    }

    /**
     * @param mixed ...$role
     * @return refreshRoles
     *
     * фактически удаляет все роли пользователя
     * затем переназначает предоставленные для него Роли
     *
     */
    public function refreshRoles(... $role )
    {
        $this->roles()->detach();
        $result = $this->roles()->attach($role);
        return $result;
    }

    /**
     * @param $permission
     * @return bool
     *
     * Проверяет, если ли права у пользователя в целом, возвращает bool
     *
     */

    public function hasPermission($permission)
    {
        return (bool) $this->permissions->where('slug', $permission)->count();
    }
    
    /**
     * @param $permission
     * @return bool
     *
     * Проверяет, если ли права у пользователя для определенного действия, возвращает bool
     *
     * $permission = $user->permissions->where('slug', 'create-tasks')->first();
     *
     */
    
    public function hasPermissionTo($permission)
    {
        return $this->hasPermissionThroughRole($permission) || $this->hasPermission($permission->slug);
    }

    /**
     * @param $permission
     * @return bool
     *
     * Проверяет, есть ли у пользователя права через его роль
     * функция проверяет, привязана ли роль с правами к пользователю
     *
     * $permission = $user->permissions->where('slug', 'create-tasks')->first();
     *
     */

    public function hasPermissionThroughRole($permission)
    {
        foreach ($permission->roles as $role){
            if($this->roles->contains($role)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param array $permissions
     * @return mixed
     *
     * Получает все права на основе переданного массива 
     *
     * $permissions = $user->permissions->all();
     *
     */
    public function getAllPermissions(array $permissions)
    {
        return Permission::whereIn('slug',$permissions)->get();
    }
    /**
     * @param mixed ...$permissions
     * @return $this
     *
     * Передаем права на выполнение определенного действия (Permissions) в виде массива и получаем все права из базы данных на основе массива
     * и сохраняем разрешения для текущего пользователя
     *
     * $user->givePermissionsTo("create-tasks")
     *
     */
    public function givePermissionsTo(... $permissions)
    {
        $permissions = $this->getAllPermissions($permissions);
        if($permissions === null) {
            return $this;
        }
        $this->permissions()->saveMany($permissions);
        return $this;
    }

    /**
     * @param mixed ...$permissions
     * @return $this
     *
     * удалить права пользователя
     *
     */
    public function deletePermissions(... $permissions )
    {
        $permissions = $this->getAllPermissions($permissions);
        $this->permissions()->detach($permissions);
        return $this;
    }
    /**
     * @param mixed ...$permissions
     * @return HasRolesAndPermissions
     *
     * фактически удаляет все права пользователя
     * затем переназначает предоставленные для него Права
     *
     */
    public function refreshPermissions(... $permissions )
    {
        $this->permissions()->detach();
        return $this->givePermissionsTo($permissions);
    }

}