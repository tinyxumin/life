<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Cookie;
use think\Session;
use app\admin\model\User;

class Index extends Controller
{
    private $user;

    /**
     * 构造方法
     */
    public function _initialize()
    {
        parent::_initialize();
        $sessionId = Session::get('token');
        $cookieId  = Cookie::get('token');
        if(empty($sessionId) || $sessionId != $cookieId){
            $this->redirect('admin/login/index');die;
        }
        $user = new User;
        $this->user = $user;
    }

    /**
     * 显示首页
     *
     * @return \think\Response
     */
    public function index()
    {
        $userId  = Cookie::get('userId');
        $where = ['userId'=>$userId];
        $res = $this->user->userLogin($where);
        return view('account/account',['userName' => $res['userName']]); 
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }
}
