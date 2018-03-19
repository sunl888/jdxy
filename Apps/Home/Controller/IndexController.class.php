<?php

namespace Home\Controller;

use Think\Controller;
use Think\Page;

/**
 * 网站首页
 *
 * @author webdd
 *
 */
class IndexController extends BaseController
{
    /**
     * @var int|mixed  分页
     */
    private $prePage;

    public function __construct()
    {
        $this->prePage = is_null(C('prePage')) ? 10 : C('prePage');
        return parent::__construct();
    }

    public function index()
    {
        $Content = D('Content');
        $classify = D('Class');

        $classes = $classify->allClasses();

        //通知公告
        $noticeClass = $classes[15];
        $classify->templateId2Info($noticeClass);
        $noticeList = $Content->getContent($noticeClass, $this->prePage);
        $this->assign('noticeClass', $noticeClass);
        $this->assign('noticeList', $noticeList);

        //学院新闻
        $hotNews = $Content->getNews(16, 5);
        $this->assign('hotNews', $hotNews);

        //获取带缩略图的新闻
        $imgNews = array_slice($hotNews , 0, 4);
        //$imgNews = $Content->getNews(0, 4, ['picurl' => ['neq', '']]);
        $this->assign('imgNews', $imgNews);

        //设置页面标题
        $this->setTitle('首页');
        $this->display();
    }
}
