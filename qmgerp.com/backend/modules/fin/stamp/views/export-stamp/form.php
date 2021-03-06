<?php
use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;
use backend\modules\fin\stamp\models\StampConfig;
use yii\helpers\Url;
$stampConfig = new StampConfig();
?>
<div class="form-container">
<?php $form = ActiveForm::begin([
    'options'=>[
        'enctype'=>'multipart/form-data',
        'class' => 'form-horizontal StampForm',
        'data-parsley-validate' => "true",
    ],
    'method' => 'post',
    'action' => $action,
    'fieldConfig' => [
        'template' => "{label}<div class=\"col-md-12\">{input}</div><div class=\"col-md-12\">{error}</div>",
        'labelOptions' => ['class' => 'col-md-3 control-label'],
    ],
])?>
    <input hidden name="StampForm[uuid]" value="<?= isset($formData['uuid'])?$formData['uuid']:''?>">
    <table class="table">
        <tbody>
        <tr>
            <td class="col-md-1">创建人</td>
            <td class="col-md-3">
                <?= Html::textInput('StampForm[created_name]',
                    isset($formData['created_name'])?$formData['created_name']:Yii::$app->user->getIdentity()->getEmployeeName(),
                    [
                        'class' => 'form-control col-md-12',
                        'disabled' => true,
                    ]) ?>
            </td>
            <td class="col-md-1">收款人</td>
            <td class="col-md-3">
                <?= Html::textInput('StampForm[payment_accept_person]',
                    isset($formData['payment_accept_person'])?$formData['payment_accept_person']:'',
                    [
                        'class' => 'enableEdit form-control col-md-12',
                        'disabled'=>isset($show)&&$show,
                    ]) ?>
            </td>
            <td class="col-md-1">发票编号*</td>
            <td class="col-md-3">
                <?= Html::textInput('StampForm[series_number]',
                    isset($formData['series_number'])?$formData['series_number']:'',
                    [
                        'class' => 'enableEdit form-control col-md-12',
                        'data-parsley-required'=>'true',
                        'disabled'=>isset($show)&&$show,
                    ]) ?>
                <?php if(isset($series_validate_error) && $series_validate_error):?>
                    <div>
                        <span style="color: red">小朋友，你输入的发票编号已经被占用，请检查!</span>
                    </div>
                <?php endif?>
            </td>

        </tr>
        <tr>
            <td>开票日期*</td>
            <td>
                <?= Html::textInput('StampForm[made_time]',
                    isset($formData['made_time'])?$formData['made_time']:'',
                    [
                        'class' => 'enableEdit input-section datetimepicker form-control col-md-12',
                        'data-parsley-required'=>'true',
                        'disabled'=>isset($show)&&$show,
                    ]) ?>
            </td>
            <td>金额*</td>
            <td>
                <?= Html::textInput('StampForm[money]',
                    isset($formData['money'])?$formData['money']:'',
                    [
                        'class' => 'enableEdit form-control col-md-12',
                        'data-parsley-required'=>'true',
                        'data-parsley-type'=>"number",
                        'disabled'=>isset($show)&&$show,
                    ]) ?>
            </td>
            <td>已匹配金额</td>
            <td>
                <?= Html::textInput('StampForm[checked_money]',
                    isset($formData['checked_money'])?$formData['checked_money']:'',
                    [
                        'class' => 'form-control col-md-12',
                        'disabled'=>true,
                    ]) ?>
            </td>
        </tr>
        <tr>
            <td>税点*</td>
            <td>
                <?= Html::textInput('StampForm[tax_point]',
                    isset($formData['tax_point'])?$formData['tax_point']:'',
                    [
                        'class' => 'enableEdit form-control col-md-12',
                        'data-parsley-required'=>'true',
                        'data-parsley-type'=>"number",
                        'disabled'=>isset($show)&&$show,
                    ]) ?>
            </td>
            <td>税费*</td>
            <td>
                <?= Html::textInput('StampForm[tax_money]',
                    isset($formData['tax_money'])?$formData['tax_money']:'',
                    [
                        'class' => 'enableEdit form-control col-md-12',
                        'data-parsley-required'=>'true',
                        'data-parsley-type'=>"number",
                        'disabled'=>isset($show)&&$show,
                    ]) ?>
            </td>
            <td>去税金额*</td>
            <td>
                <?= Html::textInput('StampForm[before_tax_money]',
                    isset($formData['before_tax_money'])?$formData['before_tax_money']:'',
                    [
                        'class' => 'enableEdit form-control col-md-12',
                        'data-parsley-required'=>'true',
                        'data-parsley-type'=>"number",
                        'disabled'=>isset($show)&&$show,
                    ]) ?>
            </td>
            <td colspan="2"></td>
        </tr>

        <tr>
            <td>发票类型</td>
            <td>
                <?= Html::dropDownList(
                    'StampForm[service_type]',
                    isset($formData['service_type'])?$formData['service_type']:null,
                    $stampConfig->getList('service_type'),
                    [
                        'class'=>'enableEdit form-control department col-md-12',
                        'disabled'=>isset($show)&&$show,
                    ]
                ) ?>
            </td>
            <td>发票状态</td>
            <td>
                <?= Html::dropDownList(
                    'StampForm[status]',
                    isset($formData['status'])?$formData['status']:null,
                    $stampConfig->getList('export_status'),
                    [
                        'class'=>'enableEdit form-control department col-md-12',
                        'disabled'=>isset($show)&&$show,
                    ]
                ) ?>
            </td>
            <td>发票性质</td>
            <td>
                <?= Html::dropDownList(
                    'StampForm[feature]',
                    isset($formData['feature'])?$formData['feature']:null,
                    $stampConfig->getList('feature'),
                    [
                        'class'=>'enableEdit form-control department col-md-12',
                        'disabled'=>isset($show)&&$show,
                    ]
                ) ?>
            </td>
        </tr>
        <tr>
            <td>收票方*</td>
            <td>
                <?= Html::textInput('StampForm[receiver]',
                    isset($formData['receiver'])?$formData['receiver']:'',
                    [
                        'class' => 'enableEdit form-control col-md-12',
                        'data-parsley-required'=>'true',
                        'disabled'=>isset($show)&&$show,
                    ]) ?>
            </td>
            <td>收票方税号</td>
            <td>
                <?= Html::textInput('StampForm[receiver_tax_code]',
                    isset($formData['receiver_tax_code'])?$formData['receiver_tax_code']:'',
                    [
                        'class' => 'enableEdit form-control col-md-12',
                        'disabled'=>isset($show)&&$show,
                    ]) ?>
            </td>
            <td>收票方地址</td>
            <td>
                <?= Html::textInput('StampForm[receiver_address]',
                    isset($formData['receiver_address'])?$formData['receiver_address']:'',
                    [
                        'class' => 'enableEdit form-control col-md-12',
                        'disabled'=>isset($show)&&$show,
                    ]) ?>
            </td>
        </tr>
        <tr>
            <td>收票方电话</td>
            <td>
                <?= Html::textInput('StampForm[receiver_phone]',
                    isset($formData['receiver_phone'])?$formData['receiver_phone']:'',
                    [
                        'class' => 'enableEdit form-control col-md-12',
                        'disabled'=>isset($show)&&$show,
                    ]) ?>
            </td>
            <td>收票方开户行</td>
            <td>
                <?= Html::textInput('StampForm[receiver_bank_of_deposit]',
                    isset($formData['receiver_bank_of_deposit'])?$formData['receiver_bank_of_deposit']:'',
                    [
                        'class' => 'enableEdit form-control col-md-12',
                        'disabled'=>isset($show)&&$show,
                    ]) ?>
            </td>
            <td>收票方账号</td>
            <td>
                <?= Html::textInput('StampForm[receiver_fin_account]',
                    isset($formData['receiver_fin_account'])?$formData['receiver_fin_account']:'',
                    [
                        'class' => 'enableEdit form-control col-md-12',
                        'disabled'=>isset($show)&&$show,
                    ]) ?>
            </td>
        </tr>
        <tr>
            <td>开票方*</td>
            <td>
                <?= Html::textInput('StampForm[provider]',
                    isset($formData['provider'])?$formData['provider']:'',
                    [
                        'class' => 'enableEdit form-control col-md-12',
                        'data-parsley-required'=>'true',
                        'disabled'=>isset($show)&&$show,
                    ]) ?>
            </td>
            <td>开票方税号</td>
            <td>
                <?= Html::textInput('StampForm[provider_tax_code]',
                    isset($formData['provider_tax_code'])?$formData['provider_tax_code']:'',
                    [
                        'class' => 'enableEdit form-control col-md-12',
                        'disabled'=>isset($show)&&$show,
                    ]) ?>
            </td>
            <td>开票方地址</td>
            <td>
                <?= Html::textInput('StampForm[provider_address]',
                    isset($formData['provider_address'])?$formData['provider_address']:'',
                    [
                        'class' => 'enableEdit form-control col-md-12',
                        'disabled'=>isset($show)&&$show,
                    ]) ?>
            </td>
        </tr>
        <tr>
            <td>开票方电话</td>
            <td>
                <?= Html::textInput('StampForm[provider_phone]',
                    isset($formData['provider_phone'])?$formData['provider_phone']:'',
                    [
                        'class' => 'enableEdit form-control col-md-12',
                        'disabled'=>isset($show)&&$show,
                    ]) ?>
            </td>
            <td>开票方开户行</td>
            <td>
                <?= Html::textInput('StampForm[provider_bank_of_deposit]',
                    isset($formData['provider_bank_of_deposit'])?$formData['provider_bank_of_deposit']:'',
                    [
                        'class' => 'enableEdit form-control col-md-12',
                        'disabled'=>isset($show)&&$show,
                    ]) ?>
            </td>
            <td>开票方账号</td>
            <td>
                <?= Html::textInput('StampForm[provider_fin_account]',
                    isset($formData['provider_fin_account'])?$formData['provider_fin_account']:'',
                    [
                        'class' => 'enableEdit form-control col-md-12',
                        'disabled'=>isset($show)&&$show,
                    ]) ?>
            </td>
        </tr>
        <tr>
            <td>快递单号</td>
            <td>
                <?= Html::textInput('StampForm[express_series_number]',
                    isset($formData['express_series_number'])?$formData['express_series_number']:'',
                    [
                        'class' => 'enableEdit form-control col-md-12',
                        'disabled'=>isset($show)&&$show,
                    ]) ?>
            </td>
            <td colspan="4"></td>
        </tr>
        <tr>
            <td>备注</td>
            <td colspan="5">
                <?= Html::textarea('StampForm[remarks]',
                    isset($formData['remarks'])?$formData['remarks']:'',
                    [
                        'class' => 'enableEdit form-control col-md-12',
                        'disabled'=>isset($show)&&$show,
                        'rows'=>3,
                    ]
                )?>
            </td>
        </tr>
        <tr>
            <td>
                附件
            </td>
            <td>
                <?= $form->field(new \backend\models\UploadForm([
                    'fileRules'=>[
                        'maxFiles' => 1,
                        'extensions'=>'bmp,jpg,jpeg,png,gif',
                    ]
                ]),'file[]')->fileInput([
                    'multiple' => true,
                    'class'=>'enableEdit',
                    'id'=>'attachment',
                    'disabled'=>isset($show)&&$show,
                ])?>
            </td>
            <td colspan="3">
                <?php if(isset($formData['attachment']) && !empty($formData['attachment'])) :?>
                    <?php
                    // 将attachment字段解析出来
                    $formData['attachment'] = unserialize($formData['attachment']);
                    ?>
                    <?php foreach($formData['attachment'] as $key=>$path) :?>
                        <div>
                            <a href="<?= Url::to([
                                '/stamp/export-stamp/attachment-download',
                                'path'=>$path,
                                'file_name'=>$key,
                            ])?>"><?= $key?></a>
                        <span class="enableEdit" style="float: right; display: <?= !isset($show) || !$show?'block':'none'?>">
                            <a href="#" url="<?= Url::to([
                                '/stamp/export-stamp/attachment-delete',
                                'path'=>$path,
                                'uuid'=>$formData['uuid'],
                            ])?>" class="attachmentDelete">删除</a>
                        </span>
                        </div>
                    <?php endforeach?>
                <?php endif?>
            </td>
            <td></td>
        </tr>
        <?php if(isset($show) && $show) :?>
        <tr class="stamp-photo">
            <td colspan="6">
                <?php if(!empty($formData['attachment'])) :?>
                    <?php foreach ($formData['attachment'] as $index=>$item) :?>
                    <span class="col-md-7">
                        <img width="100%" src="<?= Yii::getAlias('@web').'/../'.$item?>">
                    </span>
                    <?php endforeach;?>
                <?php endif;?>
            </td>
        </tr>
        <?php endif;?>
        </tbody>
    </table>
<?php if(!isset($show) || !$show) {?>
<span class="col-md-12">
<span class="col-md-3"></span>
<span class="col-md-3 enableEdit" style="display: <?= isset($show)&&$show?'none':'block'?>">
    <input type='button' value="提交不跳转" class="form-control btn-primary ajax-submit">
</span>
<span class="col-md-3 enableEdit" style="display: <?= isset($show)&&$show?'none':'block'?>">
    <input type="submit" value="提交" class="form-control btn-primary">
</span>
<span class="col-md-3"></span>
</span>
<?php } else {?>
<span class="col-md-12">
<span class="col-md-4"></span>
<span class="col-md-4 enableEdit" style="display: <?= isset($show)&&$show?'none':'block'?>">
    <input type="submit" value="提交" class="form-control btn-primary">
</span>
<span class="col-md-4"></span>
</span>
<?php }?>
<?php ActiveForm::end()?>
<?= $this->render('ajax-submit-message')?>
</div>
<?php
$JS = <<<JS
$(function() {
// ajax提交表单，并且无刷新上传文件
  $('.StampForm').on('click', '.ajax-submit', function() {
        var form = $(this).parents('form');
        var formData = new FormData(form[0]);
        formData.append('attachment', form.find('#attachment')[0].files[0]);
        $.ajax({
            url: form.attr('action'),
            type: 'post',
            data: formData,
            contentType: false, //必须false才会避开jQuery对 formdata 的默认处理 XMLHttpRequest会对 formdata 进行正确的处理  
            processData: false, //必须false才会自动加上正确的Content-Type
            success: function (data) {
                var container = form.parents('.form-container');
                var modal = container.find('.ajax-submit-message-modal');
                modal.find('.modal-body').html(data);
                modal.modal('show');
            }
        });
  });
})
JS;
$this->registerJs($JS, \yii\web\View::POS_END);
?>
