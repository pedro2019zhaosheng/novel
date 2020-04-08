<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    // 判断用户是否具有某个角色
    public function hasRole($role)
    {
        if (is_string($role)) {
            return $this->roles->contains('name', $role);
        }

        return !!$role->intersect($this->roles)->count();
    }

    // 判断用户是否具有某权限
    public function hasPermission($permission)
    {
        return $this->hasRole($permission->roles);
    }

    // 给用户分配角色
    public function assignRole($role)
    {
        return $this->roles()->save(
            Role::whereName($role)->firstOrFail()
        );
    }

    /**
     *获取用户列表
     */
    public function getUserList($where = [])
    {
        return $this->where($where)->orderBy('created_at', 'dedc')->paginate(15);
    }

    /**
     * @param $id
     * 通过id获取用户角色
     */
    public static function getRoleByUserid($id)
    {
        $roleid = DB::connection('mysql')->table('role_user')->where('user_id', $id)->value('role_id');
        return DB::connection('mysql')->table('roles')->where('id', $roleid)->first();
    }

    /**
     * @param $id
     * 通过id获取用户
     */
    public static function getUserByid($id)
    {
        return $user = DB::connection('mysql')->table('users')->where('id', $id)->first();
    }
}
