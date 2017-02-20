<?php
use yii\bootstrap\ActiveForm;
?>
<?php $form = ActiveForm::begin([
    'options'=>['enctype'=>'multipart/form-data','class' => 'form-horizontal'],
    'method' => 'post',
    'action' => ['/hr/employee-basic-config/update'],
    'fieldConfig' => [
        'template' => "{label}<div class=\"col-md-12\">{input}</div><div class=\"col-md-12\">{error}</div>",
        'labelOptions' => ['class' => 'col-md-3 control-label'],
    ],
])?>
<input hidden value="<?= $tab?>" name="Config[tab]">
<table class="table config-table">
    <thead>
        <tr>
            <td class="col-md-5">值</td>
            <td class="col-md-5">名字</td>
            <td>操作</td>
        </tr>
    </thead>
    <tbody>
        <?php if(isset($config[$key]) && !empty($config[$key])):?>
        <?php foreach($config[$key] as $index => $value) :?>
            <tr>
                <td><input class="index form-control" name="Config<?= "[" . $key."][".$index."]"?>[key]" value="<?= $index?>" readonly></td>
                <td><input class="form-control" name="Config<?= "[" . $key."][".$index."]"?>[value]" value="<?= $value?>"></td>
                <td>
                    <button type="button" class="btn-xs btn-primary removeRow"><i class="fa fa-2x fa-minus"></i></button>
                </td>
            </tr>
        <?php endforeach?>
        <?php endif?>
        <tr><td></td><td></td>
            <td>
                <button type="button" class="btn-xs btn-primary addRow" name="<?= $key?>">
                    <i class="fa fa-2x fa-plus"></i>
                </button>
            </td>
        </tr>
        <tr>
            <td colspan="3"></td>
        </tr>
    </tbody>
</table>
<span class="col-md-12">
    <span class="col-md-4">
    </span>
    <span class="col-md-4">
        <button type="submit" class="btn btn-primary col-md-12">提交</button>
    </span>
    <span class="col-md-4">
    </span>
</span>
<?php ActiveForm::end()?>