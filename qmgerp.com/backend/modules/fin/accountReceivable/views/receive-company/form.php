<?php
use yii\helpers\Html;
?>
<?= Html::beginForm(['/accountReceivable/receive-company/add'], 'post', [
    'class' => 'form-horizontal ReceiveCompanyForm',
    'data-parsley-validate' => "true",
])?>
<table class="table">
    <tbody>
    <tr>
        <td>*收款公司</td>
        <td>
            <?= Html::textInput('ReceiveCompanyForm[name]',
                isset($formData['name'])?$formData['name']:'',
                [
                    'data-parsley-required'=>'true',
                    'class'=>'form-control'
                ])?>
        </td>
        <td>*开户行</td>
        <td>
            <?= Html::textInput('ReceiveCompanyForm[bank_of_deposit]',
                isset($formData['bank_of_deposit'])?$formData['bank_of_deposit']:'',
                [
                    'data-parsley-required'=>'true',
                    'class'=>'form-control'
                ])?>
        </td>
        <td>*账号</td>
        <td>
            <?= Html::textInput('ReceiveCompanyForm[account]',
                isset($formData['account'])?$formData['account']:'',
                [
                    'data-parsley-required'=>'true',
                    'class'=>'form-control'
                ])?>
        </td>
    </tr>
    <tr><td colspan="6"></td></tr>
    </tbody>
</table>
<span class="col-md-12">
    <span class="col-md-4"></span>
    <span class="col-md-4 enableEdit" style="display: <?= isset($show)&&$show?'none':'block'?>">
        <input type="submit" value="提交" class="form-control btn-primary">
    </span>
    <span class="col-md-4"></span>
</span>
<?= Html::endForm()?>
