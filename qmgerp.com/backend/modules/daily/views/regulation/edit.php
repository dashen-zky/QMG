<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/3 0003
 * Time: 上午 1:43
 */
?>
<?php
use yii\helpers\Json;
?>
<input hidden class="menu" value='<?= Json::encode([
    'menu-1-com',
    'menu-2-regulation'
])?>'>
<div class="col-md-12" style="height: 100%">
    <ul class="nav nav-tabs">
        <li class="active"><a href="#regulation-edit-tab-1" data-toggle="tab">编辑制度</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade active in" id="regulation-edit-tab-1">
            <?php $userName = Yii::$app->user->getIdentity()->getEmployeeName()?>
            <?= $this->render('form',[
                'title'=>'添加规则制度',
                'action'=>['/daily/regulation/update'],
                'formData'=>$formData,
            ])?>
        </div>
    </div>
</div>
