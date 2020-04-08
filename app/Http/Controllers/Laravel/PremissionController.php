<?php

namespace App\Http\Controllers\Laravel;

use App\Permission;
use App\Role;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;

class PremissionController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function getIndex()
    {
        $request = Request();
        $sysrole = DB::table('permissions');
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
        $roles = $sysrole->orderBy('id','dedc')->paginate(23);

        return view('laravel/premission/index', ['page_title'=>'权限信息', 'roles'=>$roles,'where'=>$where,'id'=>$id,'name'=>$name]);
    }

    /**
     *添加权限页面
     */
    public function getPremissionAdd(Request $request){
        $page_title='权限管理';
        $actview='添加权限';
        return view('laravel/premission/premissionadd',compact('page_title','actview'));
    }

    /**
     *添加权限方法
     */
    public function postAddPremission(Request $request){
        $name=trim($request->input('name'));
        $label=trim($request->input('label'));
        $description=$request->input('description');
        $status=$request->input('status');
        if($name==''){
            return view('show',['message'=>'权限名称不能为空']);
        }
        if($label==''){
            return view('show',['message'=>'权限标签不能为空']);
        }
        $roleobj=new Permission();
        if($roleobj->getPremissionByname($name)){
            return view('show',['message'=>'权限名称已存在']);
        }
        $result=$roleobj->insert([
            'name'=>$name,
            'label'=>$label,
            'description'=>$description,
            'status'=>$status,
            'created_at'=>date('Y-m-d H:i:s'),
            'updated_at'=>date('Y-m-d H:i:s'),
        ]);
        if($result){
            return redirect('/premission');
        }else{
            return view('show',['message'=>'权限创建失败']);
        }
    }

    /**
     *编辑权限页面
     */
    public function getPremissionEdit(Request $request,$id){
        $page_title='权限管理';
        $actview='编辑权限';
        $role=DB::table('permissions')->where('id',$id)->first();
        return view('laravel/premission/premissionedit',compact('page_title','actview','role'));
    }

    /**
     *编辑权限方法
     */
    public function postEditPremission(Request $request){
        $id=$request->input('id');
        $name=trim($request->input('name'));
        $label=trim($request->input('label'));
        $description=$request->input('description');
        $status=$request->input('status');
        if($name==''){
            return view('show',['message'=>'权限名称不能为空']);
        }
        if($label==''){
            return view('show',['message'=>'权限标签不能为空']);
        }
        $roleobj=new Permission();

        if($roleobj->where('name',$name)->where('id','<>',$id)->first()){
            return view('show',['message'=>'权限名称已存在']);
        }
        $result=$roleobj->where('id',$id)->update([
            'name'=>$name,
            'label'=>$label,
            'description'=>$description,
            'status'=>$status,
            'updated_at'=>date('Y-m-d H:i:s',time()),
        ]);
        if($result){
            return redirect('/premission');
        }else{
            return view('show',['message'=>'权限创建失败,无变动']);
        }
    }

    public function getDelPremission(Request $request,$id){
        if(DB::table('permissions')->where('id',$id)->delete()){
            return redirect('/premission');
        }else{
            return view('show',['message'=>'删除失败']);
        }
    }

    /**
     *分配权限显示
     */
    public function getPremissionAssign(Request $request,$id){
        $r = new Role();
        $role=$r->getRoleByRoleid($id);
        $page_title="角色<span style='font-weight:900;color:red;'>[$role->name]</span>的权限管理";
        $actview='分配权限'; 
        $premission=new Permission();
        $PremissinList=$premission->getPremissionList();
        $rolePremission=$premission->getPremissionByroleid($id);
        return view('laravel/premission/premissionassign',compact('page_title','actview','role','PremissinList','rolePremission'));
    }

    /**
     *更新角色的权限
     */
    public function postPremissionUpdate(Request $request){
        $premission=new Permission();
        $roleid=$request->input('roleid');
        $premissionids=$request->input('pressionids');
        $premission->updatePremission($roleid,$premissionids);
        echo json_encode(['state'=>true]);
    }
}

