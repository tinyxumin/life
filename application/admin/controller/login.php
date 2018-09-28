<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Cookie;
use think\Session;
use app\admin\model\User;

class login extends Controller
{
    private $user;
     /**
     * 构造方法
     */
    public function _initialize()
    {
        parent::_initialize();
        $user = new User;
        $this->user = $user;
    }
    
    /**
     * 登陆页
     */
    public function index()
    {
        return view('login/login');
    }

    /**
     * 注册页
     */
    public function register()
    {
        return view('login/registration');
    }

    /**
     * 锁屏页面
     */
    public function lock()
    {
        $userId = Cookie::get('userId');
        if(empty($userId)){
            echo "<script>alert('登陆已过期,请重新登陆!');</script>";
            return view('login/login');
        }
        $res = $this->user->userLogin(['userId' => $userId]);
        return view('login/lock',['userName' => $res['userName'],'image' => $res['image'],'phone'=>$res['phone']]);
    }


    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $info = $request->post();
        if(empty($info['agree'])){
            echo "<script>alert('请同意徐家政策!');</script>";
            return view('login/registration');
        }
        if ($info['pas'] != $info['repas']) {
            echo "<script>alert('密码不一致!');</script>";
            return view('login/registration');
        }
        $this->user->data([
            'userName' => $info['userName'],
            'phone'    => $info['phone'],
            'password' => md5($info['repas'])
            
        ]);
        $res = $this->user->save();
        if($res){
            return $this->success('添加成功','login/index');
        }
    }

    /**
     * 登陆成功
     */
    public function loginSuccess(Request $request)
    {
        // cookie('name', null);     助手函数  删除cookie
        // session('name', null);     助手函数  删除session
        $info = $request->post();
        $where = [
            'phone'=>$info['phone'],
            'password' => md5($info['password'])
        ];
        $res = $this->user->userLogin($where);
        if($res){
            $token = md5(time()).$res['userId'];
            Cookie::set('token',$token,24*3600);
            Cookie::set('userId',$res['userId']);
            Session::set('token',$token);
            $this->user->save(['token'=>$token],['userId'=>$res['userId']]);
            return view('account/account',['userName'=>$res['userName']]);
        }else{
            return $this->error('用户名和密码错误');
        }
    }

}
