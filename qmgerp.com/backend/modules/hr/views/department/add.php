<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/3 0003
 * Time: 下午 5:55
 */
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\helpers\Url;
use yii\web\View;
?>
<div class="panel panel-body department-form">
    <?= $this->render('department-form',[
        'action'=>['/hr/department/add'],
        'formData'=>[],
    ])?>
    <?= $this->render('/department/department-select-list-panel',[
        'selectId' => "parentDepartmentSelect",
    ])?>
</div>

