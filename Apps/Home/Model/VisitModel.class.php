<?php

namespace Home\Model;

use Think\Model;

/**
 * Created by PhpStorm.
 * User: Sunlong
 * Date: 2017/5/17
 * Time: 16:59
 */
class VisitModel extends Model
{

    public function getVisit()
    {
        $view = $this->field('SUM(view) as view_count')->select();
        return $view[0]['view_count'];
    }
}
