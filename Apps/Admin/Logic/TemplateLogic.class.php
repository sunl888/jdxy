<?php

namespace Admin\Logic;

use Think\Model;

class TemplateLogic extends Model
{
    public function get_all_template()
    {
        return $this->select();
    }
}

?>