<?php
use yii\widgets\ActiveForm;
?>
<?php $form = ActiveForm::begin([
    'options'=>['enctype'=>'multipart/form-data','class' => 'form-horizontal ' . $formClass],
    'method' => 'post',
    'enableAjaxValidation'=>true,
    'enableClientValidation'=>true,
    'action' => $action,
    'fieldConfig' => [
        'template' => "{label}<div class=\"col-md-12\">{input}</div><div class=\"col-md-12\">{error}</div>",
        'labelOptions' => ['class' => 'col-md-3 control-label'],
    ],
])?>
    <table class="table supplier-table">
        <tbody>
        <tr>
            <td>旧密码</td>
            <td>
                <?= $form->field($model,'old_password')->passwordInput([
                    'autofocus' => true,
                ])->label(false)?>
            </td>
        </tr>
        <tr>
            <td>新密码</td>
            <td>
                <?= $form->field($model,'new_password')->passwordInput([
                    'autofocus' => true,
                ])->label(false)?>
            </td>
        </tr>
        <tr>
            <td>确认密码</td>
            <td>
                <?= $form->field($model,'verify_password')->passwordInput([
                    'autofocus' => true,
                ])->label(false)?>
            </td>
        </tr>
        <tr>
            <td></td>
            <td>
                <input type="submit" value="提交" class="form-control btn btn-primary">
            </td>
        </tr>
        </tbody>
    </table>
<?php ActiveForm::end()?>