<?php
use yii\helpers\Html;
use yii\helpers\Url;
use backend\modules\fin\stamp\models\StampConfig;
$config = new StampConfig();
?>
    <!-- #modal-without-animation -->
<div class="modal fade apply-stamp-modal">
<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title">申请开票</h4>
    </div>
    <div class="modal-body">
        <?= Html::beginForm(['/crm/project-apply-stamp/apply'], 'post', [
            'class' => 'form-horizontal ApplyStampForm',
            'data-parsley-validate' => "true",
        ])?>
        <?= Html::input('hidden', 'ApplyStampForm[project_uuid]', null, [
            'class'=>'project-uuid'
        ])?>
        <table class="table">
            <tr>
                <td class="col-md-1">项目金额</td>
                <td class="col-md-3">
                    <input data-parsley-required="true"
                           class="form-control project-money"
                           type="text" disabled>
                </td>
                <td class="col-md-1">已开票金额</td>
                <td class="col-md-3">
                    <input data-parsley-required="true"
                           class="form-control checked-stamp-money"
                           type="text" disabled>
                </td>
                <td class="col-md-1">可开票金额</td>
                <td class="col-md-3">
                    <input data-parsley-required="true"
                           class="form-control rest-money"
                           type="text" disabled>
                </td>
            </tr>
            <tr>
                <td>金额*</td>
                <td>
                    <div>
                        <input class="form-control money"
                               type="text"
                               name="ApplyStampForm[money]"
                               data-parsley-required="true"
                               data-parsley-type="number">
                    </div>
                    <span style="color: red" class="money-error"></span>
                </td>
                <td>开票类型</td>
                <td>
                    <?= Html::dropDownList('ApplyStampForm[type]', null,
                        $config->getList('service_type'),[
                            'class'=>'form-control'
                        ])?>
                </td>
                <td>开票性质</td>
                <td>
                    <?= Html::dropDownList('ApplyStampForm[feature]', null,
                        $config->getList('feature'),[
                            'class'=>'form-control'
                        ])?>
                </td>
            </tr>
            <tr>
                <td>选择开票信息*</td>
                <td class="select-stamp-message"></td>
                <td>备注</td>
                <td colspan="3">
                    <?= Html::textInput('ApplyStampForm[remarks]',null,[
                        'class'=>'form-control'
                    ])?>
                </td>
            </tr>
            <tr>
                <td>开票信息*</td>
                <td colspan="5">
                    <?= Html::textarea("stamp_message_area", null, [
                        'class'=>"form-control stamp-message-area",
                        'data-parsley-required'=>"true",
                        'rows'=>3,
                        'disabled'=>true,
                    ])?>
                </td>
            </tr>
            <tr>
                <td colspan="6"></td>
            </tr>
        </table>
    <span class="col-md-12">
        <span class="col-md-4"></span>
        <span class="col-md-4">
            <?= Html::button('提交', [
                'class'=>'form-control btn-primary submit'
            ])?>
        </span>
        <span class="col-md-4"></span>
    </span>
        <?= Html::endForm()?>
    </div>
    <div class="modal-footer">
    </div>
</div>
</div>
<?php
$Js = <<<JS
$(function() { 
    $('.apply-stamp-modal').on('click', '.submit',function() {
        var form = $(this).parents('form');
        var money = form.find('.money').val();
        var rest_money = form.find('.rest-money').val();
        if(parseFloat(rest_money) < parseFloat(money)) {
            form.find('.money-error').html('小盆友，输入的金额不可以大于可开票金额哦^^^');
            return false;
        }
        form.submit();
    });
})
JS;
$this->registerJs($Js, \yii\web\View::POS_END);
?>