<?php

namespace Home\Controller;

use Think\Controller;


class BaseController extends Controller
{
    protected $webTitle = '';
    protected $currentClassId = null;
    /**
     *  构造方法
     */
    public function __construct()
    {
        parent::__construct();

        $this->loginCount();

        //获取网站信息
        $this->getWebInfo();

        //获取首页大图
        $this->getBigPic();

        //获取页脚参数
        $this->getFooter();

        //获取顶部大图
        $this->getTopPic();

        //获取网站浏览量
        $this->getVisit();
    }

    //获取首页大图

    /**
     *  统计网站流量函数
     */
    public function loginCount()
    {
        $ip = get_client_ip();
        $time = time();

        //判断当前用户是否任然存在于session中
        if (is_null(session(md5($ip)))) {
            $Visit = M('Visit');
            $condition['ip'] = $ip;
            $v = $Visit->where($condition)->order('time desc')->find();
            if (is_null($v)) {
                $data['ip'] = $ip;
                $data['time'] = $time;
                $data['view'] = 1;
                $data['y'] = date('y', $time);
                $data['m'] = date('m', $time);
                $data['d'] = date('d', $time);
                $Visit->add($data);
                unset($data);
            } else {
                $vid = $v['vid'];
                $lastTime = $v['time'];
                $y = date('y', $lastTime);
                $m = date('m', $lastTime);
                $d = date('d', $lastTime);
                $y_ = date('y', $time);
                $m_ = date('m', $time);
                $d_ = date('d', $time);

                if ($y == $y_ && $m == $m_ && $d == $d_) {
                    //用户当天访问量加1
                    $Visit->where('vid = ' . $vid)->setInc('view');
                } else {
                    $data['ip'] = $ip;
                    $data['time'] = $time;
                    $data['view'] = 1;
                    $data['y'] = $y_;
                    $data['m'] = $m_;
                    $data['d'] = $d_;
                    $Visit->add($data);
                }
            }
            //给session赋值
            session(md5($ip), 'hnjrxxw');
        }
        return;
    }

    /**
     * 获取网站信息
     */
    public function getWebInfo()
    {
        $Setting = M('Setting');
        $webInfoArr = $Setting->select();
        $webInfo = array();
        //处理网站信息数组
        foreach ($webInfoArr as $key => $val) {
            $webInfo[$val['item']] = $val['value'];
        }
        $this->assign('webInfo', $webInfo);
    }

    public function getBigPic()
    {
        //$this->clearCache('bigs');
        if (($bigPics = $this->hasCache('bigs'))) {
            $this->assign('bigPics', $bigPics);
        } else {
            $bigPic = M('big_pic');
                    $bigPics = $bigPic->order('sort_index asc, addtime desc')->select();
            $this->setCache('bigs', $bigPics);
        }
        $this->assign('bigPics', $bigPics);
    }

    /**
     * 判断session、cookie中有没有要查找的键
     * @param $option
     * @return bool
     */
    public function hasCache($option)
    {
        if (empty($_SESSION[$option])) {
            if (empty($_COOKIE[$option])) {
                return false;
            } else {
                $_SESSION[$option] = unserialize($_COOKIE[$option]);
            }
        }
        return $_SESSION[$option];
    }

    /**
     * 设置session、cookie
     * @param $option
     * @param $value
     */
    public function setCache($option, $value)
    {
        $_SESSION[$option] = $value;
        setcookie($option, serialize($value), time() + 3600);
    }

    /**
     * 获取页面页脚内容
     */
    public function getFooter()
    {
        $Flink = M('Flink');

        //获取底部图片链接
        //todo 这里暂时写死
        $condition['type_id'] = 1;
        $faculty = $Flink->where($condition)->limit(10)->select();
        unset($condition);

        //获取首页悬浮窗
        $condition['type_id'] = 2;
        $xfc = $Flink->where($condition)->find();
        unset($condition);

        //获取院系设置友链
        $condition['type_id'] = 3;
        $setUrl = $Flink->where($condition)->limit(10)->select();
        unset($condition);

        //获取友情链接
        $condition['type_id'] = 4;
        $flinkUrl = $Flink->where($condition)->limit(10)->select();
        unset($condition);

        $this->assign('faculty', $faculty);
        $this->assign('flinkUrl', $flinkUrl);
        $this->assign('setUrl', $setUrl);
        $this->assign('xfc', $xfc);
    }

    /**
     * 随机从数据库中获取一张顶部图片并存入cookie中
     */
    public function getTopPic()
    {
        $this->clearCache('top_pic');
        if (($top_pic = $this->hasCache('top_pic'))) {
            $this->assign('welcome', $top_pic);
        } else {
            $condition['type_id'] = 5;
            $Flink = M('Flink');
            $getTopPic = $Flink->where($condition)->limit(10)->select();
            $top_pic = $getTopPic[mt_rand(1, count($getTopPic)) - 1];
            $this->assign('welcome', $top_pic);
            $this->setCache('top_pic', $top_pic);
        }
    }

    public function getVisit()
    {
        $this->clearCache('view_count');
        if (($view_count = $this->hasCache('view_count'))) {
            $this->assign('view', $view_count);
        } else {
            $visit = D('Visit');
            $view_count = $visit->getVisit();
            //$_SESSION['view_count'] = $view_count;
            $this->setCache('view_count',$view_count);
        }
        $this->assign('visit', $view_count);
    }

    /**
     * 清除session、cookie指定的键
     * @param $option
     */
    public function clearCache($option)
    {
        unset($_SESSION[$option]);
        setcookie($option, null, time() - 1);
    }

    /**
     * 复写父类的display方法
     * @param string $templateFile
     * @param string $charset
     * @param string $contentType
     * @param string $content
     * @param string $prefix
     */
    protected function display($templateFile = '', $charset = '', $contentType = '', $content = '', $prefix = '')
    {
        $this->assign('title', $this->webTitle);
        $this->getNav();
        parent::display($templateFile, $charset, $contentType, $content, $prefix);
    }

    /**
     * 获取页面导航栏
     */
    public function getNav()
    {
        $Class = D('Class');
        $nav = $Class->getNav($this->currentClassId);
        $this->assign('currentClassId', $this->currentClassId);
        $this->assign('navList', $nav);
    }

    /**
     * 设置当前网页标题
     * @param $title
     */
    protected function setTitle($title)
    {
        $this->webTitle = $title;
    }

    /**
     * 设置当前栏目id
     * @param $currentClassId
     */
    protected function setCurrentClassId($currentClassId)
    {
        $this->currentClassId = $currentClassId;
    }
}
