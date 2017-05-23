<?php
/**
 * Mark: 网站开发，软件开发，远程运维
 * Email:zhaojunlike@gmail.com
 * Date: 2016/12/28
 * Time: 19:36
 */

namespace Admin\Controller;


use Think\Page;
use Think\Upload;

class AppController extends AdminController
{
    protected function _initialize()
    {
        parent::_initialize(); // TODO: Change the autogenerated stub
        $this->assign('_nav_show_id', "#app-nav");
    }


    public function version($p)
    {

    }

    //推送更新
    public function push()
    {
        if (IS_POST) {
            $app = M('app_version')->create();
            if (!$app) {
                $this->error("push失败");
            }
            //上传app文件
            $upConf = C('APP_UPDATE_UPLOAD');
            $upModel = new Upload($upConf);
            $upRet = $upModel->upload();
            echo $upConf["rootPath"];
            if (!$upRet) {
                $this->error("请上传APP文件,否则不给予更新!!!" . $upModel->getError());
            }
            $upRet = $upRet['app_file'];
            $upload['name'] = $upRet['name'];
            $upload['savename'] = $upRet['savename'];
            $upload['savepath'] = $upRet['savepath'];
            $upload['ext'] = $upRet['ext'];
            $upload['mime'] = $upRet['mime'];
            $upload['size'] = $upRet['size'];
            $upload['md5'] = $upRet['md5'];
            $upload['sha1'] = $upRet['sha1'];
            $upload['time'] = time();
            $upload['path'] = "./Uploads/" . $upRet['savepath'] . $upRet['savename'];
            $upAddRet = M('system_upload')->add($upload);
            if (!$upAddRet) {
                $this->error("文件保存失败");
            }
            $app['upload_id'] = $upAddRet;
            $app['push_time'] = time();
            $app['create_time'] = time();
            $addAppRet = M('app_version')->add($app);
            $this->success("推送更新成功", U('app/update'));
        } else {
            $this->display("ea_app");
        }
    }

    public function banner($p = 1)
    {
        $map = array();
        $data_list = M('app_banner v')
            ->join('LEFT JOIN ey_system_upload u ON u.id=v.upload_id')
            ->where($map)
            ->page($p, $this->page_limit)
            ->field('v.*,u.path as img_path')
            ->order("id DESC")
            ->select();

        $count = M('app_banner v')
            ->join('LEFT JOIN ey_system_upload u ON u.id=v.upload_id')
            ->where($map)
            ->count();

        $pModel = new Page($count, $this->page_limit);

        $this->assign('page', $pModel->show());
        $this->assign('data_list', $data_list);
        $this->display();
    }

    public function add_banner()
    {
        if (IS_POST) {
            $app = M('app_banner')->create();
            if (!$app) {
                $this->error("push失败");
            }
            //上传app文件
            $upConf = C('APP_BANNER_UPLOAD');
            $upModel = new Upload($upConf);
            $upRet = $upModel->upload();
            if (!$upRet) {
                $this->error("请上传图片!!!" . $upModel->getError());
            }
            $upRet = $upRet['app_file'];
            $upload['name'] = $upRet['name'];
            $upload['savename'] = $upRet['savename'];
            $upload['savepath'] = $upRet['savepath'];
            $upload['ext'] = $upRet['ext'];
            $upload['mime'] = $upRet['mime'];
            $upload['size'] = $upRet['size'];
            $upload['md5'] = $upRet['md5'];
            $upload['sha1'] = $upRet['sha1'];
            $upload['time'] = time();
            $upload['path'] = "./Uploads/" . $upRet['savepath'] . $upRet['savename'];
            $upAddRet = M('system_upload')->add($upload);
            if (!$upAddRet) {
                $this->error("文件保存失败");
            }
            $app['upload_id'] = $upAddRet;
            $app['create_time'] = time();
            $addAppRet = M('app_banner')->add($app);
            $this->success("放置广告成功", U('app/banner'));
        } else {
            $this->display("ea_banner");
        }
    }

    public function del_banner($type = 2)
    {
        $ids = I('ids');
        $userIds = array();
        if (!is_array($ids)) {
            $userIds[] = $ids;
        } else {
            $userIds = array_values($ids);
        }
        $userMap['id'] = array('in', $userIds);
        $upRet = false;
        switch ($type) {
            case 1:
                $upRet = M('app_banner')->where($userMap)->setField(array('del_status' => 1));
                break;
            case 2:
                $upRet = M('app_banner')->where($userMap)->delete();
                break;
        }
        if ($upRet) {
            $this->success("删除成功");
        } else {
            $this->error("删除失败");
        }
    }

    public function tokens()
    {

    }

    public function del_app($type = 2)
    {
        $ids = I('ids');
        $userIds = array();
        if (!is_array($ids)) {
            $userIds[] = $ids;
        } else {
            $userIds = array_values($ids);
        }
        $userMap['id'] = array('in', $userIds);
        $upRet = false;
        switch ($type) {
            case 1:
                $upRet = M('app_version')->where($userMap)->setField(array('del_status' => 1));
                break;
            case 2:
                $upRet = M('app_version')->where($userMap)->delete();
                break;
        }
        if ($upRet) {
            //TODO 删除文件
            $this->success("删除成功");
        } else {
            $this->error("删除失败");
        }
    }

    public function update($p = 1)
    {
        $map = array();
        $data_list = M('app_version v')
            ->join('LEFT JOIN ey_system_upload u ON u.id=v.upload_id')
            ->where($map)
            ->page($p, $this->page_limit)
            ->field('v.*,u.path as app_path')
            ->order("id DESC")
            ->select();

        $count = M('app_version v')
            ->join('LEFT JOIN ey_system_upload u ON u.id=v.upload_id')
            ->where($map)
            ->count();
        $pModel = new Page($count, $this->page_limit);
        $this->assign('page', $pModel->show());
        $this->assign('data_list', $data_list);
        $this->display();
    }

}