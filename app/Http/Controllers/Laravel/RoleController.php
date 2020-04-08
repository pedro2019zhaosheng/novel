<?php

namespace App\Http\Controllers\Laravel;

use App\Role;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\models\UserInfo;
use DB;

class RoleController extends Controller
{

    public function callAction($method, $parameters)
    {
        $r=new Role();
        $r->initRoleSuper();
        return call_user_func_array([$this, $method], $parameters);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function getIndex()
    {
        $request = Request();
        $sysrole = DB::table('roles');
        $where = [];
        $id =  $request->input('id', '');
        if ($id && is_numeric($id)) {
            $where['id'] = $id;
            $sysrole=$sysrole->where('id',$id);
        }
        $name =  trim($request->input('name', ''));
        if ($name && !empty($name)) {
            $where['name'] = $name;
            $sysrole=$sysrole->where('name','like','%'.$name.'%');
        }
        $roles = $sysrole->orderBy('id','dedc')->paginate(23);;

        return view('laravel/role/index', ['page_title'=>'角色信息', 'roles'=>$roles,'where'=>$where,'id'=>$id,'name'=>$name]);
    }

    /**
     *添加角色页面
     */
    public function getRoleAdd(Request $request){
        $page_title='角色管理';
        $actview='添加角色';
        return view('laravel/role/roleadd',compact('page_title','actview'));
    }

    /**
     *添加角色方法
     */
    public function postAddRole(Request $request){
        $name=$request->input('name');
        $label=$request->input('label');
        $description=$request->input('description');
        if($name==''){
            return view('show',['message'=>'角色名称不能为空']);
        }
        $roleobj=new Role();
        if($roleobj->getRoleByRolename($name)){
            return view('show',['message'=>'角色名称已存在']);
        }
        $result=$roleobj->insert([
            'name'=>$name,
            'label'=>$label,
            'description'=>$description,
            'created_at'=>date('Y-m-d H:i:s'),
            'updated_at'=>date('Y-m-d H:i:s'),
        ]);
        if($result){
            return redirect('/role');
        }else{
            return view('show',['message'=>'角色创建失败']);
        }
    }

    /**
     *编辑角色页面
     */
    public function getRoleEdit(Request $request,$id){
        $page_title='角色管理';
        $actview='编辑角色';
        $role=DB::table('roles')->where('id',$id)->first();
        return view('laravel/role/roleedit',compact('page_title','actview','role'));
    }

    /**
     *编辑角色方法
     */
    public function postEditRole(Request $request){
        $id=$request->input('id');
        $name=$request->input('name');
        $label=$request->input('label');
        $description=$request->input('description');
        if($id==1){
            return view('show',['message'=>'超级管理员角色不可更改！']);
        }
        if($name==''){
            return view('show',['message'=>'角色名称不能为空']);
        }
        $roleobj=new Role();

        if($roleobj->where('name',$name)->where('id','<>',$id)->first()){
            return view('show',['message'=>'角色名称已存在']);
        }
        $result=$roleobj->where('id',$id)->update([
            'name'=>$name,
            'label'=>$label,
            'description'=>$description,
            'updated_at'=>date('Y-m-d H:i:s',time()),
        ]);
        if($result){
            return redirect('/role');
        }else{
            return view('show',['message'=>'角色创建失败,无变动']);
        }
    }

    public function getDelRole(Request $request,$id){
        if($id==1){
            return view('show',['message'=>'超级管理员角色不可删除！']);
        }
        if(DB::table('roles')->where('id',$id)->delete()){
            return redirect('/role');
        }else{
            return view('show',['message'=>'删除失败']);
        }
    }
}

