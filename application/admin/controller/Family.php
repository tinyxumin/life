<?php

namespace app\admin\controller;

use app\admin\model\User;
//use think\Cache;
use think\Controller;
use think\Request;

class Family extends Controller
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
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $userId = cookie('userId');
        $userInfo = $this->user->userLogin(['userId'=>$userId]);
        $data = $this->user->userInfo();
        $userName = cookie('userName');
        return view('account/family',['data'=>$data,'userName' => $userName,'userInfo'=>$userInfo]);
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
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        $info = $this->user->userLogin(['userId'=>$id]);
        return view('account/userEdit',['data'=>$info]);
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
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
    public function update(Request $request, $userId)
    {
        $data = array();
        $post = $request->post();
        if ($post['password'] != $post['repas']){
            return $this->error('密码不一致');
        }
        if (!empty($post['password'])){
            $data['password'] = md5($post['password']);
        }
        if (empty($post['image'])){
            $data['image'] = '/default.jpg';
        }
        $file = request()->file('image');
        // 移动到框架应用根目录/public/uploads/ 目录下
        if($file){
            $info = $file->validate(['size'=>15678000,'ext'=>'jpg,png,gif'])->move(ROOT_PATH . 'public' . DS . 'uploads');
            if($info){
                // 成功上传后 获取上传信息
                // 输出 jpg
//                echo $info->getExtension();
                $data['image'] = '/uploads/'.$info->getSaveName();
                // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
//                echo $info->getSaveName();
                // 输出 42a79759f284b767dfcb2a0197904287.jpg
//                echo $info->getFilename();
            }else{
                // 上传失败获取错误信息
                return $this->error($file->getError());
            }
        }
        $data['userName'] = $post['userName'];
        $data['phone'] = $post['phone'];
        $data['sex'] = $post['sex'];
        $data['age'] = $post['age'];
        $data['birthday'] = $post['birthday'];
        $data['address'] = $post['address'];
        $this->user->save($data,['userId' => $post['userId']]);
        return redirect('admin/family/index');
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        $result = $this->user->userDel($id);
        if ($result > 0) {
            $info['status'] = true;
            $info['id'] = $id;
            $info['info'] = 'ID为' . $id . '的用户删除成功!';
        } else {
            $info['status'] = false;
            $info['id'] = $id;
            $info['info'] = 'ID为' . $id . '的用户删除失败!';
        }
        // JSON 返回
        return json($info);

    }
}
