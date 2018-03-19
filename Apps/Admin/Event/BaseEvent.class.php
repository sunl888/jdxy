<?php

namespace Admin\Event;

use Think\Controller;
use Think\Image;


class BaseEvent extends Controller
{

    protected $uploadPath;
    protected $admin;

    public function __construct()
    {
        parent::__construct();
        $this->uploadPath = $_SERVER['DOCUMENT_ROOT'] . '/CMS/Public/upload/';
        $this->getSessionAdmin(); //获取存储在session中的用户
    }


    /**
     * 获取用户session信息标记
     */
    private function getSessionAdmin()
    {
        $SessionLogic = D('Session', 'Logic');
        $session = $SessionLogic->checkSession(get_client_ip());
        $this->admin = session($session['name']);
    }

    /**
     * 系统文件上传函数
     * @param unknown_type $type 文件上传类型
     * @param unknown_type $thumb 是否生成缩略图
     * @param unknown_type $path 返回路径还是上传文件对象
     * @return String | Object
     */
    function upload($type, $thumb = false, $path = true, array $size = [])
    {
        // 上传文件类型
        $ext_arr = array(
            'image' => array('gif', 'jpg', 'jpeg', 'png', 'bmp'),
            'photo' => array('jpg', 'jpeg', 'png'),
            'flash' => array('swf', 'flv'),
            'media' => array('swf', 'flv', 'mp3', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb'),
            'file' => array('doc', 'docx', 'xls', 'xlsx', 'ppt', 'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2', 'pdf')
        );
        try{
            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize = 0;// 设置附件上传大小
            $upload->subName = array('date', 'Ymd');
            $upload->subType = 'hash';//使用日期模式创建子目录
            $upload->allowExts = $ext_arr[$type];// 设置附件上传类型
            $upload->rootPath = "./Public/upload/";
            $upload->savePath = $type . "/";// 设置附件上传目录
            $upload->saveRule = 'uniqid';
            $upload->thumb = $thumb;//生成缩略图
            $upload->thumbMaxWidth = isset($size['width'])?$size['width']:1920;//缩略图最大宽度
            $upload->thumbMaxHeight = isset($size['height'])?$size['height']:1080;//缩略图最大高度
            $upload->thumbRemoveOrigin = false;
            // 上传文件
            $info = $upload->upload();
            //todo  这里本来想加水印的，但是GD库容易发生内存溢出，所以暂时不加水印
            /*$file = $upload->rootPath.$info[$type]['savepath'].$info[$type]['savename'];
            if(file_exists($file)){
                $image = new Image(Image::IMAGE_GD);
                $image->open($file)->thumb($upload->thumbMaxWidth,$upload->thumbMaxHeight,Image::IMAGE_THUMB_SCALE)->water('./Public/images/water.png',\Think\Image::IMAGE_WATER_SOUTHEAST,60)->save($file);
            }*/
            // 上传成功 获取上传文件信息
            if ($path) {
                foreach ($info as $file) {
                    return $file['savepath'] . $file['savename'];
                }
            } else {
                return $info;
            }
        }catch (Exception $e){
            $this->error($e->getMessage());
        }

    }

}
