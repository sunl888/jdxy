<?php

namespace Home\Model;

use Think\Model;

class ClassModel extends Model
{

    protected $tableName = 'class';
    protected static $parents = [];
    private $classes = null;

    /**
     * 获取所有的栏目
     * @return null
     */
    public function allClasses()
    {
        if (is_null($this->classes)) {
            $data = $this->order('sort_index asc, class_id asc')->select();
            foreach ($data as $v) {
                $this->classes[$v['class_id']] = $v;
            }
        }
        return $this->classes;
    }

    /**
     * 返回指定ID对应的所有子栏目id
     * @param $id
     * @return array
     */
    public function getChildClass($id)
    {
        $classIdArr = array();
        $condition['father_id'] = $id;
        $classArr = $this->where($condition)->select();
        $maxClassId = $classArr[0]['class_id'];
        for($i = 0;$i<count($classArr);$i++)
            for($j = 0;$j<count($classArr);$j++)
            {
                if($classArr[$i]['class_id'] < $maxClassId){
                    $maxClassId = $classArr[$i]['class_id'];
                }
            }
        /*foreach ($classArr as $k => $v) {
            $classIdArr[] = $v['class_id'];
        }*/
        //todo 2017/6/23 【更新】 选择一级栏目直接显示一级栏目下的第一个二级栏目列表
        $classIdArr[] = $maxClassId;
        $classIdArr[] = $id;
        return $classIdArr;
    }

    public function getNav($currentClassId)
    {
        $condition['is_show'] = 1;
        $condition['is_nav'] = 1;
        $data = $this->where($condition)
            ->order('sort_index asc, class_id asc')
            ->alias('c')
            ->join("__TEMPLATE__ t ON c.index_template = t.template_id")
            ->field('c.class_id,c.father_id,c.name as class_name,t.type,c.channel_id,t.url,c.content_template,c.index_template,t.template_id,t.name as template_name')
            ->select();
        foreach ($data as $v) {
            $nav[$v['class_id']] = $v;
        }
        unset($data);
        if (!is_null($currentClassId)) {
            foreach ($nav as &$val) {

                if ($val['class_id'] == $currentClassId) {
                    if ($val['father_id'] == 0) {
                        $val['active'] = true;
                    } else {
                        $nav[$val['father_id']]['active'] = true;
                    }
                } else if (!isset($val['active'])) {
                    $val['active'] = false;
                }
            }
        }
        unset($val);
        foreach ($nav as $item => &$val) {

            $this->templateId2Info($val);
            if ($val['father_id'] != 0) {
                $nav[$val['father_id']]['child'][] = $val;
                unset($nav[$item]);
            }
        }
        unset($val);
        return $nav;
    }

    /**
     * 获取所有的父级栏目
     * @return array
     */
    public function getParents()
    {
        if (count(self::$parents) <= 0) {
            //todo ['is_nav'=>1]
            self::$parents = $this->where([['father_id' => 0], ['is_show' => 1]])->order('sort_index asc, class_id asc')->select();
        }
        return self::$parents;
    }

    public function getChildClassArr($parentClassId, $onlyGetIds = false)
    {
        $res = [];
        foreach ($this->allClasses() as $k => $v) {
            if ($v['father_id'] == $parentClassId && $v['is_show'] == 1) {
                $this->templateId2Info($v);
                $res[$v['class_id']] = $onlyGetIds ? $v['class_id'] : $v;
            }
        }
        return $res;
    }

    /**
     * 将记录中的index_template和content_template里保存的模板id改为对应的url
     * @param $class
     */
    public function templateId2Info(&$class)
    {
        if (!is_null($class)) {
            $templates = $this->getTemplates();
            $class['index_template'] = $templates[$class['index_template']];
            $class['content_template'] = $templates[$class['content_template']];
        }
    }

    /**
     * 获取模板表中的所有数据(template_id为数组的键)
     * @return array|null
     */
    public function getTemplates()
    {
        static $templates = null;
        if (is_null($templates)) {
            $data = M('template')->select();
            foreach ($data as $v) {
                $templates[$v['template_id']] = $v;
            }
            unset($data);
        }
        return $templates;
    }

}