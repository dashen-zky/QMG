<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/18 0018
 * Time: 下午 8:35
 */

namespace backend\models;


use yii\widgets\LinkPager;
use yii\helpers\Html;
class MyLinkPage extends LinkPager
{
    public $ser_filter;
    public function renderPageButton($label, $page, $class, $disabled, $active)
    {
        $options = ['class' => empty($class) ? $this->pageCssClass : $class];
        if ($active) {
            Html::addCssClass($options, $this->activePageCssClass);
        }
        if ($disabled) {
            Html::addCssClass($options, $this->disabledPageCssClass);

            return Html::tag('li', Html::tag('span', $label), $options);
        }
        $linkOptions = $this->linkOptions;
        $linkOptions['data-page'] = $page;
if(!$this->pagination instanceof MyPagination) {
    var_dump($this->pagination);die;
}
        return Html::tag('li', Html::a($label, $this->pagination->createFilterUrl($page,$this->ser_filter), $linkOptions), $options);
    }
}