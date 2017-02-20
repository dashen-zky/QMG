<?php
use yii\widgets\ActiveForm;
$form = ActiveForm::begin([
    'options'=>['enctype'=>'multipart/form-data','class' => 'form-horizontal ' . $formClass],
    'method' => 'post',
    'action' => $action,
    'fieldConfig' => [
        'template' => "{label}<div class=\"col-md-12\">{input}</div><div class=\"col-md-12\">{error}</div>",
        'labelOptions' => ['class' => 'col-md-3 control-label'],
    ],
])?>