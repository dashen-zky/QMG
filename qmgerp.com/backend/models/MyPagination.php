<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/18 0018
 * Time: 下午 8:37
 */

namespace backend\models;


use yii\data\Pagination;

class MyPagination extends Pagination {
    public function createFilterUrl($page, $ser_filters)
    {
        $this->params['ser_filter'] = $ser_filters;
        return parent::createUrl($page);
    }
}