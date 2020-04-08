<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\models\BaseModel;
use App\User;
use Illuminate\Http\Request;
use App\models\Withdrawals;
use Illuminate\Support\Facades\Auth;
use App\models\Ip;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function getIndex()
    {
        return view('index', ['role' => User::getRoleByUserid(Auth::user()->id)->name]);
    }

    public function getLeft()
    {
        $roleid = BaseModel::factory('role_user')->where('user_id', Auth::user()->id)->value('role_id');
        $userid = Auth::user()->id;
        // 数据统计角色区分
        if ($roleid == 3) {
            if ($userid == 3) {
                $pack_name = 1;
            } elseif ($userid == 4) {
                $pack_name = 2;
            } elseif ($userid == 5) {
                $pack_name = 3;
            } elseif ($userid == 6) {
                $pack_name = 4;
            } elseif ($userid == 7) {
                $pack_name = 5;
            } elseif ($userid == 8) {
                $pack_name = 6;
            } else {
                $pack_name = 0;
            }
        } else {
            $pack_name = 0;
        }

        return view('left', ['pack_name' => $pack_name]);
    }

    public function getHome()
    {
        $model = BaseModel::factory('user_log');
        $logincount = $model->where('userid', Auth::user()->id)->where('desc', '用户登录成功!')->count();
        $lastlogin = $model->where('userid', Auth::user()->id)->where('desc', '用户登录成功!')->orderBy('created_at', 'desc')->first();

        if ($lastlogin) {
            $ip = long2ip($lastlogin->ip);
            $ips = new Ip();
            $ip_arr = $ips->find($ip);
            if ($ip_arr) {
                $str = " ";
                $str .= $ip_arr['0'] . ' ';
                $str .= $ip_arr['1'] . ' ';
                $str .= $ip_arr['2'] . ' ';
                $lastlogin->ipaddr = $ip . '  ' . $str;
            } else {
                $lastlogin->ipaddr = $ip;
            }
        }
        $role = User::getRoleByUserid(Auth::user()->id)->name;
        return view('home', ['logincount' => $logincount, 'lastlogin' => $lastlogin, 'role' => $role]);
    }


    public function getShare(){
        $request = Request();
        $id= $request->input('id',0);
        return view('share',[ 'where' => $id]);
    }
}
