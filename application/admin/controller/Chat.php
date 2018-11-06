<?php
    namespace app\admin\controller;

    use think\Controller;
    use app\admin\model\User;


    class Chat extends Controller
    {
        private $user;

        public function _initialize()
        {
            parent::_initialize();
            $user = new User();
            $this->user = $user;
            $this->userInfo = $this->user->userLogin(['userId'=>cookie('userId')]);
        }

        public function index()
        {
            return view('chat/chat',['userInfo' => $this->userInfo]);
        }
    }