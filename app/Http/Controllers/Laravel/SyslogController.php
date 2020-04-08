<?php

namespace App\Http\Controllers\Laravel;

use App\Role;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class SyslogController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function getIndex()
    {
        $request = Request();
        $syslog = DB::table('user_log');
        $where = [];
        $name =  trim($request->input('name', ''));
        if ($name && !empty($name)) {
            $where['name'] = $name;
            $syslog=$syslog->where('name','like','%'.$name.'%');
        }
        $start =  $request->input('start');
        if ($start) {
            $where['start'] = $start;
            $syslog=$syslog->where('created_at','>',$start);
        }

        $end =  $request->input('end');
        if ($end) {
            $where['end'] = $end;
            if(date('H',strtotime($end))=='00' && date('i',strtotime($end))=='00' && date('s',strtotime($end))=='00'){
                $pend = strtotime($end) + 86400;
            }else{
                $pend = strtotime($end);
            }
            $syslog=$syslog->where('created_at','<',date('Y-m-d H:i:s',$pend));
        }
        $logs = $syslog->orderBy('created_at','dedc')->paginate(2);

        return view('laravel/syslog/index', ['page_title'=>'系统日志信息', 'logs'=>$logs,'where'=>$where,'start'=>$start,'name'=>$name,'end'=>$end]);
    }
}

