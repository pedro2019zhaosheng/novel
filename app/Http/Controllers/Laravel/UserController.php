<?php

namespace App\Http\Controllers\Laravel;

use App\Role;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Illuminate\Support\Facades\Auth;
use Request as Ret;

class UserController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function getIndex()
    {
        $request = Request();
        $sysuser = DB::table('users');
        $where = [];
        $id =  $request->input('id', '');
        if ($id && is_numeric($id)) {
            $where['id'] = $id;
            $sysuser=$sysuser->where('id',$id);
        }
        $name =  trim($request->input('name', ''));
        if ($name && !empty($name)) {
            $where['name'] = $name;
            $sysuser=$sysuser->where('name','like','%'.$name.'%');
        }
        $email =  trim($request->input('email', ''));
        if ($email && !empty($email)) {
            $where['email'] = $email;
            $sysuser=$sysuser->where('email','like','%'.$email.'%');
        }
        $users = $sysuser->orderBy('id','dedc')->paginate(23);;
        foreach($users as &$vp){
            $role=User::getRoleByUserid($vp->id);
            if($role){
                $vp->role=$role->name;
            }else{
                $vp->role='未分配角色';
            }

        }

        return view('laravel/user/user', ['page_title'=>'用户信息', 'users'=>$users,'where'=>$where,'id'=>$id,'name'=>$name,'email'=>$email]);
    }

    /**
     *添加用户页面
     */
    public function getUserAdd(Request $request){
        $page_title='用户管理';
        $actview='添加用户';
        $roleobj=new Role();
        $rolelist=$roleobj->getRolist();
        return view('laravel/user/useradd',compact('page_title','actview','rolelist'));
    }

    /**
     *添加用户方法
     */
    public function postAddUser(Request $request){
        $data['name']=$request->input('name');
        $data['email']=$request->input('email');
        $data['password']=$request->input('password');
        $roleid=$request->input('roleid');
        $repassword=$request->input('repassword');
        if($repassword!=$data['password']){
            return view('show',['message'=>'两次密码输入不一致']);
        }elseif($data['name']=='' || $data['email']==''){
            return view('show',['message'=>'用户名或账号不能为空']);
        }elseif($data['password']=='' ){
            return view('show',['message'=>'密码不能为空']);
        }
        $sysuser = DB::table('users');
        if($sysuser->where('email',$data['email'])->first()){
            return view('show',['message'=>'用户名已存在']);
        }
        $result=User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        $roleuser=DB::table('role_user')->insert(['role_id'=>$roleid,'user_id'=>$result->id]);

        if($result && $roleuser){
            return redirect('/sysuser');
        }else{
            DB::table('role_user')->where('id',$result->id)->delete();
            return view('show',['message'=>'创建失败']);
        }
    }

    public function getDelUser(Request $request,$id){
        if(DB::table('users')->where('id',$id)->delete()){
            return redirect('/sysuser');
        }else{
            return view('show',['message'=>'删除失败']);
        }
    }

    /**
     *编辑用户页面
     */
    public function getUserEdit(Request $request,$id){
        $page_title='用户管理';
        $actview='编辑用户';
        $roleobj=new Role();
        $rolelist=$roleobj->getRolist();
        $user=User::getUserByid($id);
        $user->role=User::getRoleByUserid($id)?User::getRoleByUserid($id)->id:'';
        return view('laravel/user/useredit',compact('page_title','actview','rolelist','user'));
    }

    /**
     *编辑用户方法
     */
    public function postEditUser(Request $request){
        $id=$request->input('id');
        $data['name']=$request->input('name');
        $data['email']=$request->input('email');
        $data['updated_at']=date('y_m-d H:i:s');
        $password=$request->input('password');
        $roleid=$request->input('roleid');
        $repassword=$request->input('repassword');
        if($repassword!=$password){
            return view('show',['message'=>'两次密码输入不一致']);
        }elseif($data['name']=='' || $data['email']==''){
            return view('show',['message'=>'用户名或账号不能为空']);
        }elseif($password!='' ){
            $data['password']=bcrypt($password);
        }
        $sysuser = DB::table('users');
        if($sysuser->where('email',$data['email'])->where('id','<>',$id)->first()){
            return view('show',['message'=>'用户名已存在']);
        }
        $result=DB::table('users')->where('id',$id)->update($data);
        $roleup=DB::table('role_user');
        if($roleup->where('user_id',$id)->first()){
            if($roleid!=''){
                $roleuser=$roleup->where('user_id',$id)->update(['role_id'=>$roleid]);
            }else{
                $roleuser=$roleup->where('user_id',$id)->delete();
            }
        }else{
            if($roleid!='') {
                $roleuser = $roleup->insert(['role_id' => $roleid, 'user_id' => $id]);
            }
        }

        if($result || $roleuser){
            return redirect('/sysuser');
        }else{
            return view('show',['message'=>'更新失败,无变动']);
        }
    }

    /**
     *编辑用户方法
     */
    public function postPassword(Request $request){
        $id=Auth::user()->id;
        $data['updated_at']=date('y_m-d H:i:s');
        $password=$request->input('password');
        $repassword=$request->input('repassword');

        if($repassword!=$password){
            return view('show',['message'=>'两次密码输入不一致']);
        }elseif($password!='' ){
            $data['password']=bcrypt($password);
        }
        $result=DB::table('users')->where('id',$id)->update($data);
        if($result){
            saveSyslog($id,Auth::user()->name,'更新密码',ip2long(Ret::getClientIp()),date('Y-m-d H:i:s'));
            return view('show',['message'=>'更新密码成功']);
        }else{
            return view('show',['message'=>'更新失败,无变动']);
        }
    }
}

