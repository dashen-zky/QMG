<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
?>
<!-- begin #page-container -->
<div id="page-container" class="fade">
    <!-- begin login -->
    <div class="login bg-black animated fadeInDown">
        <!-- begin brand -->
        <div class="login-header" style="width: 700px">
            <div class="brand">
                <span class="logo"></span>谦玛ERP
            </div>

            <div style="font-size: 14px">
                <?php $sign = [
                    '轻轻地我来了，正如我轻轻的我的走，挥一挥衣袖，不带来一片云彩，却带走了两个***',
                    '找点时间，找点空闲，常回ERP看看',
                    '我哒哒的马蹄是个美丽的错误，我不是归人，是个P客',
                    '何以解忧，唯有ERP',
                    '无言独上ERP，月如钩，寂寞梧桐深院锁清秋',
                    '剪不断，理还乱，是离愁。别是一般滋味在心头',
                    '春花秋月何时了？ERP知多少',
                    '李白上ERP不给钱， 我把李白踹下船',
                    '上个ERP,交个P友',
                    '动感地带，我的ERP，我作主',
//                    '输入“老王”，开启翻墙模式，免登陆，自动进入ERP',
                    'ctrl+f4, 进入免登陆模式'
                ]?>
                <i><?= $sign[rand(0,count($sign)-1)]?></i>
            </div>
        </div>
        <!-- end brand -->
        <div class="login-content">
            <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

            <?= $form->field($model, 'username')->textInput(['autofocus' => true])->label('用戶名') ?>

            <?= $form->field($model, 'password')->passwordInput()->label('密碼') ?>

            <?= $form->field($model, 'rememberMe')->checkbox() ?>

            <div class="form-group">
                <?= Html::submitButton('登录', ['class' => 'btn btn-primary col-lg-12', 'name' => 'login-button']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
    <!-- end login -->
</div>
<!-- end page container -->
<div style="text-align: center">
    Copyright 2016 www.qmgerp.com. All Rights Reserved 上海谦玛网络科技股份有限公司版权所有 <a href="http://www.miitbeian.gov.cn" target="_blank">沪ICP备11022867号</a>
</div>
