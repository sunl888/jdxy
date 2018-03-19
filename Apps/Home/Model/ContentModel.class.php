<?php

namespace Home\Model;

use Think\Model;

class ContentModel extends Model
{
    /**
     * 根据栏目ID，获取指定数量的新闻列表
     *
     * @param $class
     * @param $amount
     * @return $newsList
     */
    public function getContent($class, $amount, $exceptContentId = null)
    {
        $classify = D('Class');
        if ($class['father_id'] == 0) {
            $childIds = [];
            foreach ($classify->getChildClassArr($class['class_id']) as $v) {
                $childIds[] = $v['class_id'];
            }
            array_push($childIds, $class['class_id']);
            $condition['class_id'] = ['in', $childIds];
        } else {
            $condition['class_id'] = $class['class_id'];
        }

        if (!is_null($exceptContentId)) {
            $condition['content_id'] = ['NEQ', intval($exceptContentId)];
        }
        $condition['state'] = 'publish';
        $newsList = $this->where($condition)->order('sort_index asc, addtime desc, content_id desc')->limit($amount)->select();
        return $newsList;
    }

    /**
     * 根据文章id获取文章内容
     * @param int $id
     * @return mixed
     */
    public function getArticleById($id = 0)
    {
        $content = M('con_article');
        return $content->find($id);
    }

    /**
     * 获取指定栏目下相应数量的焦点图
     *
     * @param $id
     * @param $amount
     * @return $jdtList
     */
    public function getJdt($id, $amount)
    {
        $condition['class_id'] = $id;
        $condition['picurl'] = array('neq', '');
        $jdtList = $this->where($condition)->order('addtime desc')->limit($amount)->select();
        return $jdtList;
    }

    /**
     * @param $id 指定栏目
     * @param $amount  limit
     * @param array $condition 条件
     * @return mixed 结果集
     */
    public function getNews($id, $amount, $condition = [])
    {
        !$id ?: $attachCondition['class_id'] = $id;
        $attachCondition['state'] = 'publish';
        $condition = array_merge($condition, $attachCondition);
        //todo is_stick表示是否置顶
        $news = $this->where($condition)->order('is_stick desc,addtime desc')->limit($amount)->select();
        return $news;
    }

    /**
     * 搜索
     * @param $keyword
     * @param $limit default =0 查询所有
     * @return mixed
     */
    public function search($keyword, $offset = 0, $limit = 0)
    {
        $condition['state'] = ['eq', 'publish'];
        $condition['title'] = array('like', "%$keyword%");
        $join1 = "JOIN __CLASS__ ON __CONTENT__.class_id = __CLASS__.class_id";
        $join2 = "JOIN __TEMPLATE__ ON __CLASS__.index_template = __TEMPLATE__.template_id";
        return $this->join($join1)->join($join2)->limit($offset, $limit)->where($condition)->select();
    }
}