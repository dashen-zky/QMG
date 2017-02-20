<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use backend\models\ViewHelper;
use backend\modules\fin\accountReceivable\models\ReceiveMoneyCompany;
use yii\helpers\Url;
?>
<?php $form = ActiveForm::begin([
    'options'=>[
        'enctype'=>'multipart/form-data',
        'class' => 'form-horizontal AccountReceivable',
        'data-parsley-validate' => "true",
    ],
    'method' => 'post',
    'action' => $action,
    'fieldConfig' => [
        'template' => "{label}<div class=\"col-md-12\">{input}</div><div class=\"col-md-12\">{error}</div>",
        'labelOptions' => ['class' => 'col-md-3 control-label'],
    ],
])?>
<?php if(Yii::$app->authManager->canAccess(
    \backend\modules\rbac\model\PermissionManager::FinancialMenu
)) :?>
<?php endif;?>
<input hidden value="<?= isset($formData['uuid'])?$formData['uuid']:''?>" name="AccountReceivable[uuid]">
<input hidden value="<?= isset($scenario)?$scenario:'default'?>" name="scenario">
<table class="table">
<tbody>
    <tr>
        <td class="col-md-1">银行流水号*</td>
        <td class="col-md-3">
            <?= $form->field($model,'bank_series_number')->
            textInput([
                'value'=>isset($formData['bank_series_number'])?$formData['bank_series_number']:'',
                'data-parsley-required'=>'true',
                'class'=>'enableEdit form-control',
                'disabled'=>isset($show)&&$show,
            ])->
            label(false)?>
            <div class="bank_series_number_error error_alter" style="display: none;"></div>
        </td>
        <td class="col-md-1">金额*</td>
        <td class="col-md-3">
            <?= $form->field($model,'money')->
            textInput([
                'value'=>isset($formData['money'])?$formData['money']:'',
                'data-parsley-required'=>'true',
                'class'=>'enableEdit form-control',
                'disabled'=>isset($show)&&$show,
            ])->
            label(false)?>
        </td>
        <td class="col-md-1">录入人</td>
        <td class="col-md-3">
            <?= $form->field($model,'created_name')->
            textInput([
                'value'=>isset($formData['created_name'])?$formData['created_name']:'',
                'disabled'=>true,
            ])->
            label(false)?>
        </td>
    </tr>
    <tr>
        <td>录入时间</td>
        <td>
            <?= $form->field($model,'time')->
            textInput([
                'value'=>
                    (isset($formData['time']) && $formData['time'] !='0')?date("Y-m-d", $formData['time']):'',
                'disabled'=>true,
            ])->
            label(false)?>
        </td>
        <td>收款时间</td>
        <td>
            <?= $form->field($model,'receive_time')->
            textInput([
                'disabled'=>isset($show)&&$show,
                'class'=>'form-control input-section datetimepicker enableEdit',
                'value'=>
                    (isset($formData['receive_time']) && $formData['receive_time'] !='0')?date("Y-m-d", $formData['receive_time']):'',
            ])->
            label(false)?>
        </td>
        <td>付款方*</td>
        <td colspan="3">
            <?= $form->field($model,'payment')->
            textInput([
                'value'=>isset($formData['payment'])?$formData['payment']:'',
                'data-parsley-required'=>'true',
                'class'=>'enableEdit form-control',
                'disabled'=>isset($show)&&$show,
            ])->
            label(false)?>
        </td>
    </tr>
    <tr>
        <td>收款公司*</td>
        <td>
            <?php
            if(!isset($receiveCompanyList)) {
                $receiveMoneyCompany = new ReceiveMoneyCompany();
                $receiveCompanyList = $receiveMoneyCompany->receiveMoneyCompanyList();
                $receiveCompanyList = $receiveMoneyCompany->transformForDropDownList($receiveCompanyList, 'uuid', 'name');
            }
            ?>
            <?= Html::dropDownList('AccountReceivable[receive_company_uuid]',
                isset($formData['receive_company_uuid'])?$formData['receive_company_uuid']:null,
                ViewHelper::appendElementOnDropDownList($receiveCompanyList), [
                    'class'=>'form-control receive-company-uuid',
                    'data-parsley-required'=>'true',
                    'url'=>\yii\helpers\Url::to([
                        '/accountReceivable/receive-company/load-receive-company-information',
                    ])
                ])?>
        </td>
        <td>开户行*</td>
        <td>
            <input 
                data-parsley-required=true 
                class="form-control bank-of-deposit" 
                value="<?= isset($formData['receive_money_company_bank_of_deposit'])?$formData['receive_money_company_bank_of_deposit']:''?>"
                disabled>
        </td>
        <td>收款账号*</td>
        <td>
            <input data-parsley-required=true
                   value="<?= isset($formData['receive_money_company_account'])?$formData['receive_money_company_account']:''?>"
                   class="form-control account" disabled>
        </td>
    </tr>
    <tr>
        <td>备注*</td>
        <td colspan="5">
            <?= $form->field($model,'remarks')->textarea([
                'rows'=>3,
                'value'=>isset($formData['remarks'])?$formData['remarks']:'',
                'class'=>'enableEdit form-control',
                'disabled'=>isset($show)&&$show,
                'data-parsley-required'=>'true',
            ])->label(false)?>
        </td>
    </tr>
    <tr>
        <td>凭证*</td>
        <td>
            <?= $form->field($model,'file[]')->fileInput([
                'multiple' => true,
                'data-parsley-required'=>'true',
            ])->label(false)?>
        </td>
        <td colspan="2">
            <?php if(isset($formData['path']) && !empty($formData['path'])) :?>
                <?php
                // 将attachment字段解析出来
                $paths = unserialize($formData['path']);
                ?>
                <?php foreach($paths as $key=>$path) :?>
                    <div>
                        <?= $key?>
                        <span class="enableEdit" style="float: right; display: none">
                            <a href="#" url="<?= Url::to([
                                '/accountReceivable/receive-money/evidence-delete',
                                'path'=>$path,
                                'uuid'=>$formData['uuid'],
                            ])?>" name="<?= $key?>" class="attachmentDelete">删除</a>
                        </span>
                    </div>
                <?php endforeach?>
            <?php endif?>
        </td>
        <td colspan="2"></td>
    </tr>
    <?php if(isset($formData['path']) && !empty($formData['path'])) :?>
    <tr>
        <td colspan="6" class="evidence-img">
            <?php $path = unserialize($formData['path']);?>
            <?php foreach ($path as $index=>$item) :?>
                <span class="col-md-6">
                    <img width="100%" name="<?= $index?>" src="<?= Yii::getAlias('@web').'/../'.$item?>">
                </span>
            <?php endforeach;?>
        </td>
    </tr>
    <?php endif;?>
</tbody>
</table>

<span class="col-md-12">
    <span class="col-md-4"></span>
    <span class="col-md-4 enableEdit" style="display: <?= isset($show)&&$show?'none':'block'?>">
        <input type="button" value="提交" class="form-control btn-primary submit" validate-url="<?= Url::to([
            '/accountReceivable/receive-money/form-validate'
        ])?>">
    </span>
    <span class="col-md-4"></span>
</span>
<?php ActiveForm::end()?>
<?php
$JS = <<<JS
$(function() {
    $('.AccountReceivable').on('change', '.receive-company-uuid', function() {
        var self = $(this);
        
        var form = self.parents('form');
        form.find('.account').val('');
        form.find('.bank-of-deposit').val('');
        var url = self.attr('url') + '&uuid=' + self.val();
        $.get(url, function(data, status) {
            if(status !== 'success') {
                return ;
            }
            
            data = JSON.parse(data);
            form.find('.account').val(data.account);
            form.find('.bank-of-deposit').val(data.bank_of_deposit);
        })
    });
    
    // 表单数据验证
    $('.AccountReceivable').on('click', '.submit', function() {
        var validate_url = $(this).attr('validate-url');
        var form = $(this).parents('form');
        $.ajax({
            url: validate_url,
            type: 'post',
            data: form.serialize(),
            success: function (data) {
                if (data == 1) {
                    form.submit();
                }

                var error_alert = form.find('.error-alert').css({display:'none'});
                data = JSON.parse(data);
                $.each(data, function(key, value) {
                    var error_field = form.find('.'+ key + '_error');
                    error_field.html(value[0]);
                    error_field.css({display:'block'});
                });
            }
        });
    });
})
JS;
$this->registerJs($JS, \yii\web\View::POS_END);
?>