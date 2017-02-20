<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/29 0029
 * Time: 下午 5:54
 */

namespace backend\models;


use yii\widgets\LinkPager;
use yii\helpers\Html;

class AjaxLinkPage extends LinkPager
{
    public $ser_filter;
    public function renderPageButton($label, $page, $class, $disabled, $active)
    {
        $options = [
            'class' => empty($class) ? $this->pageCssClass : $class,
            'url' => $this->pagination->createFilterUrl($page,$this->ser_filter),
        ];
        if ($active) {
            Html::addCssClass($options, $this->activePageCssClass);
        }
        if ($disabled) {
            Html::addCssClass($options, $this->disabledPageCssClass);

            return Html::tag('li', Html::tag('span', $label), $options);
        }
        $linkOptions = $this->linkOptions;
        $linkOptions['data-page'] = $page;

        return Html::tag('li', Html::a($label, 'javascript:;', $linkOptions), $options);
    }
}