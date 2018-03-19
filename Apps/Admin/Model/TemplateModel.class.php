<?php

namespace Admin\Model;

use Think\Model;

/**
 * 模板模型层
 */
class TemplateModel extends Model
{
    public $_validate = array(
        array(
            'name',
            'require',
            '模板名称不能为空!',
            1
        ),
        //todo 2017.5.13 孙龙修改  这里不用用url作为验证规则
        /*array(
            'url',
            'url',
            '链接地址格式错误!',
            1
        ),*/
    );
}