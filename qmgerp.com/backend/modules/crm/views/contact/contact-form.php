<?php
use yii\widgets\ActiveForm;
use yii\web\View;
use backend\modules\crm\models\customer\model\ContactForm;
?>
<?php $form = ActiveForm::begin([
    'options'=>[
        'enctype'=>'multipart/form-data',
        'class' => 'form-horizontal ' . $formClass,
        'data-parsley-validate' => "true",
    ],
    'method' => 'post',
    'action' => ['/crm/contact/update'],
    'fieldConfig' => [
        'template' => "{label}<div class=\"col-md-12\">{input}</div><div class=\"col-md-12\">{error}</div>",
        'labelOptions' => ['class' => 'col-md-3 control-label'],
    ],
])?>
<?= $form->field($model,'oldUuids')->
input('hidden',[
    'value'=>isset($oldUuids)?$oldUuids:''
])->label(false)?>
<!--// type里面存放是负责人还联系人,在跟新数据的时候要知道的-->
<?= $form->field($model,'type')->
input('hidden',['value'=>$type])->label(false)?>
<table class="table contact-table">
    <?php $i = 0;?>
    <?php if( !empty($list)) :?>
        <?php  foreach($list as $contact):?>
            <?= $this->render('contact-block',[
                'i'=>$i,
                'form'=>$form,
                'model'=>$model,
                'operator'=>'delContactRow',
                'contact'=>$contact,
            ]);?>
            <?php $i++?>
        <?php endforeach?>
    <?php else:?>
        <?= $this->render('contact-block',[
            'i'=>$i,
            'form'=>$form,
            'model'=>$model,
            'contact'=>'',
            'type'=>$type,
        ]);?>
    <?php endif?>
</table>
<span class="col-md-12">
    <span class="col-md-4"></span>
    <span class="col-md-4">
        <input type="button" value="提交" class="editContactFormSubmit form-control btn-primary">
    </span>
    <span class="col-md-4"></span>
</span>
<?php ActiveForm::end()?>