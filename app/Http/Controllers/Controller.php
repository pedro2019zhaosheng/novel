<?php

namespace App\Http\Controllers;

use App\Permission;
use App\Role;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


use Auth;
use Gate;
use Request;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth',['except' => [ 'getRegister', 'getSendcode' ]]);
        $path = Request::path();
        $path = explode('/', $path);
        $path = isset($path[1]) ? $path[0] . '/' . $path[1] : $path[0];
        if ($path == 'home' || $path == 'show' || $path == 'home/left' || $path == 'home/home') {
            return;
        }
        if (isset(Auth::user()->id)) {
            $user = Auth::user();
            $pression = new Permission();
            $isSetlog = $pression->getPremissionByname($path) ? $pression->getPremissionByname($path)->status : 0;
            if ($isSetlog == 1) {
                saveSyslog($user->id, $user->name, $pression->getPremissionByname($path) ? $pression->getPremissionByname($path)->label : '权限设置错误', ip2long(Request::getClientIp()), date('Y-m-d H:i:s'));
            }
            $roleid = Role::getRoleByid(Auth::user()->id) ? Role::getRoleByid(Auth::user()->id)->role_id : '';
            if ($roleid == 1) {
                return;
            }

            if (!Gate::forUser($user)->allows($path)) {
                if (Request::isMethod('post')) {
                    echo json_encode(['state' => false]);
                } else {
                    echo view('permissions');
                }
                exit();
            }
        }
    }

    public function getUTime()
    {
        list($usec, $sec) = explode(' ', microtime());
        $time = ((float)$usec + (float)$sec);
        return $time;
    }
}
