<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Cookie;
use think\Session;
use app\admin\model\User;
use app\admin\model\Diary;

class Daily extends Controller
{
    private $diary;
    private $user;
    private $userInfo;
    /**
     * 构造方法
     */
    public function _initialize()
    {
        parent::_initialize();
        $diary = new Diary();
        $user = new User();
        $this->diary = $diary;
        $this->user = $user;
        $this->userInfo = $this->user->userLogin(['userId'=>cookie('userId')]);
    }

    /**
     * 显示首页
     *
     * @return \think\Response
     */
    public function index()
    {
        $data = $this->diary->moments();
        return view('daily/daily',['userInfo' => $this->userInfo,'data'=>$data]);
    }

    /**
     * 发布动态
     * @param Request $request
     * @return \think\response\Redirect|void
     */
    public function save(Request $request)
    {
        $post = $request->post();
        $file = \request()->file('photo');
//        dump($file);die;
        $photos = array();
        $types = array();
        if($file){
            foreach ($file as $v){
                $info = $v->move(ROOT_PATH . 'public' . DS . 'uploads');
                if($info){
                    // 成功上传后 获取上传信息
                    // 输出 jpg
//                echo $info->getExtension();
                    $types[] = $info->getExtension();
                    $photos[] = '/uploads/'.$info->getSaveName();
                    // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
//                echo $info->getSaveName();
                    // 输出 42a79759f284b767dfcb2a0197904287.jpg
//                echo $info->getFilename();
                }else{
                    // 上传失败获取错误信息
                    return $this->error($v->getError());
                }
            }
        }
        $data['diary'] = $post['diary'];
        $data['photo'] = implode(',',$photos);
        $data['type'] = implode(',',$types);
        $data['userId'] = $this->userInfo['userId'];
        $data['userName'] = $this->userInfo['userName'];
        $data['image'] = $this->userInfo['image'];
        $data['addTime'] = date('Y-m-d H:i:s');
        if (empty($data['diary']) && empty($data['photo'])){
            return $this->error('请填写动态信息');die;
        }
        $this->diary->save($data);
        return redirect('admin/daily/index');
    }

    /**
     * 用户记录
     * @return \think\response\View
     */
    public function userRecord()
    {
        $where = [
            'userId'=>$this->userInfo['userId']
        ];
        $data = $this->diary->userMoment($where);
        $date = date('Y-m-d');
        $yesterday = date('Y-m-d 00:00:00',strtotime('-1 day'));
        return view('daily/timeline',['userInfo' => $this->userInfo,'data'=>$data,'date'=>$date,'yesterday'=>$yesterday]);
    }

    /**
     * 删除用户动态  2018-10-23 11:40
     * @param Request $request
     * @return mixed
     */
    public function comDel(Request $request)
    {
        $post = $request->post();
        $result = $this->diary->comDel($post['cid']);
        if ($result > 0){
            $info['status'] = true;
            $info['data'] = '删除成功';
        }else{
            $info['status'] = false;
        }
        return $info;
    }

    public function updateState(Request $request)
    {
        $post = $request->post();
        $data = ['status'=>$post['state']];
        $res = $this->diary->save($data,['id'=>$post['comid']]);
        return $post;


    }

}
