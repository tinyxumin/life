<?php

namespace app\admin\controller;

use app\admin\model\Photo;
use app\admin\model\PhotoGroup;
use app\admin\model\User;
//use think\Cache;
use think\Controller;
use think\Request;

class Family extends Controller
{
    private $user;
    private $group;
    private $photos;

    /**
     * 构造方法
     */
    public function _initialize()
    {
        parent::_initialize();
        $user = new User;
        $group = new PhotoGroup();
        $photos = new Photo();
        $this->user = $user;
        $this->group = $group;
        $this->photos = $photos;
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
        $post = $request->post();
        if ($post['password'] != $post['repas']){
            return $this->error('密码不一致');
        }
        if (empty($post)){
            return $this->error('请添加内容');
        }
        $post['password'] = md5($post['password']);
        $this->user->allowField(true)->save($post);
        return redirect('admin/family/index');
    }

    /**
     * 新建资源页面
     */
    public function add()
    {
        $userId = cookie('userId');
        $userInfo = $this->user->userLogin(['userId'=>$userId]);
        $userName = cookie('userName');
        return view('account/userAdd',['userName'=>$userName,'userInfo'=>$userInfo]);

    }

    /**
     * 显示指定的资源
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        $userId = cookie('userId');
        $userInfo = $this->user->userLogin(['userId'=>$userId]);
        $info = $this->user->userLogin(['userId'=>$id]);
        $userName = cookie('userName');
        return view('account/userEdit',['data'=>$info,'userName'=>$userName,'userInfo'=>$userInfo]);
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

    /**
     * 家庭照片
     * @param
     * @return \think\Response
     */
    public function photos()
    {
        $userId = cookie('userId');
        $userInfo = $this->user->userLogin(['userId'=>$userId]);
        $userName = cookie('userName');
        $groups = $this->group->group();
        $gName = array();
        foreach ($groups as $val){
            $gName[$val['id']] = $val['name'];
        }
        $data = $this->photos->photo();
        foreach($data as $v){
            if (!empty($gName[$v['gid']])){
                $v['gid'] = $gName[$v['gid']];
            }
        }
        return view('account/photos',['groups'=>$groups,'userName'=>$userName,'userInfo'=>$userInfo,'data'=>$data]);

    }

    /**
     * 修改保存家庭照片
     * @param
     * @return \think\Response
     */
    public function editPhotos(Request $request)
    {
        $userId = cookie('userId');
        $userInfo = $this->user->userLogin(['userId'=>$userId]);
        $userName = cookie('userName');
        $post = $request->post();
        $this->photos->save($post,['id'=>$post['id']]);
        return redirect('admin/family/photos');

    }

    /**
     * 删除指定资源
     * @param  int  $id
     * @return \think\Response
     */
    public function delPhoto(Request $request)
    {
        $post = $request->post();
        $result = $this->photos->photoDel($post['id']);
        return redirect('admin/family/photos');
    }

    /**
     * 上传家庭照片
     * @param
     * @return \think\Response
     */
    public function uploadPhotos(Request $request)
    {
        $userId = cookie('userId');
        $userInfo = $this->user->userLogin(['userId'=>$userId]);
        $userName = cookie('userName');
        $file = request()->file('image');
        // 移动到框架应用根目录/public/uploads/ 目录下
        if($file){
            $info = $file->validate(['size'=>156780000,'ext'=>'jpg,png,gif'])->move(ROOT_PATH . 'public' . DS . 'photos');
            if($info){
                // 成功上传后 获取上传信息
                // 输出 jpg
//                echo $info->getExtension();
                $data['image'] = '/photos/'.$info->getSaveName();
                // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
//                echo $info->getSaveName();
                // 输出 42a79759f284b767dfcb2a0197904287.jpg
//                echo $info->getFilename();
            }else{
                // 上传失败获取错误信息
                return $this->error($file->getError());die;
            }
        }
        $data['author'] = $userName;
        $data['size'] = $info->getSize();
        $data['type'] = $info->getExtension();
        $data['addTime'] = date('Y-m-d H:i:s');
        $this->photos->save($data);
        return redirect('admin/family/photos');

    }
}
