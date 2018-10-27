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
        $userName  = Cookie::get('userName');
        $userId  = Cookie::get('userId');
        $data = $this->user->userLogin(['userId'=>$userId]);
        return view('account/account',['userName' => $userName,'data'=>$data]);
    }

}
