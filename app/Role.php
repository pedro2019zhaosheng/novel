<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Role extends Model
{
    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    //给角色添加权限
    public function givePermissionTo($permission)
    {
        return $this->permissions()->save($permission);
    }

    public function getRolist()
    {
        return $this->get();
    }

    public function initRoleSuper()
    {
        $sup = $this->where('id', 1)->first();
        if (!$sup) {
            $this->insert([
                'id' => 1,
                'name' => '超级管理员',
                'label' => 'root super',
                'description' => '系统root管理，只读',
            ]);
        }
    }

    /**
     * @param $name
     * @return mixed
     * 通过name获取角色
     */
    public function getRoleByRolename($name)
    {
        return $this->where('name', $name)->first();
    }

    /**
     * @param $name
     * @return mixed
     * 通过id获取角色
     */
    public static function getRoleByid($id)
    {
        return DB::table('role_user')->where('user_id', $id)->first();
    }

    /**
     * @param $name
     * @return mixed
     * 通过id获取角色
     */
    public function getRoleByRoleid($id)
    {
        return $this->where('id', $id)->first();
    }


}
