<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Permission extends Model
{
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * @param $name
     * @return mixed
     * 通过name获取角色
     */
    public function getPremissionByname($name)
    {
        return $this->where('name', $name)->first();
    }

    /**
     *获取分组权限列表
     */
    public function getPremissionList()
    {
        $arrlist = $this->get()->toArray();
        $grouplist = [];
        foreach ($arrlist as &$v) {
            $name = explode('/', $v['name']);
            if (isset($name[0]) && !in_array($name[0], $grouplist)) {
                $grouplist[$name[0]] = [];
            }
        }
        foreach ($arrlist as &$v) {
            $name = explode('/', $v['name']);
            $grouplist[$name[0]][] = $v;
        }
        return $grouplist;
    }

    /**
     * @param $roleid
     */
    public function updatePremission($roleid, $premissionids)
    {
        $db = DB::table('permission_role');
        $db->where('role_id', $roleid)->delete();
        foreach ($premissionids as $preid) {
            $db->insert(['role_id' => $roleid, 'permission_id' => $preid]);
        }
    }

    /**
     * @param $roleid
     */
    public function getPremissionByroleid($roleid)
    {
        $db = DB::table('permission_role');
        return $db->where('role_id', $roleid)->lists('permission_id');
    }
}
