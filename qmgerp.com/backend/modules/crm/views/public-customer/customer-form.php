<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\web\View;
use backend\modules\crm\models\customer\model\PublicCustomerForm;
use backend\models\ViewHelper;
use backend\modules\crm\models\customer\model\BusinessModel;
use backend\modules\crm\models\customer\model\CustomerConfig;
$config = new CustomerConfig();
$viewHelper = new ViewHelper($model);
?>
<?php $form = ActiveForm::begin([
    'options'=>[
        'enctype'=>'multipart/form-data',
        'class' => 'form-horizontal',
        'data-parsley-validate' => "true",
    ],
    'id'=>'CustomerForm',
    'method' => 'post',
    'action' => $action,
    'fieldConfig' => [
        'template' => "{label}<div class=\"col-md-12\">{input}</div><div class=\"col-md-12\">{error}</div>",
        'labelOptions' => ['class' => 'col-md-3 control-label'],
    ],
])?>

<?= $form->field($model,'uuid')->
input('hidden',[
    'value'=>isset($formData['uuid'])?$formData['uuid']:''
])->label(false)?>
<?php
if(Yii::$app->authManager->isAuthor(
    Yii::$app->user->getIdentity()->getId(),
    isset($formData['created_uuid'])?$formData['created_uuid']:''
)) :?>
<?php if($show):?>
    <div style="float: right">
        <a href="javascript:;" class="editForm" style="font-size: 15px"><i class="fa fa-2x fa-pencil"></i>编辑</a>
    </div>
<?php endif?>
<?php endif?>
<?= $form->field($model,'contactUuids')->
input('hidden',[
    'class'=>'contactUuids',
    'value'=>isset($formData['contactUuids'])?$formData['contactUuids']:''
])->label(false)?>
<?= $form->field($model,'dutyUuids')->
input('hidden',[
    'value'=>isset($formData['dutyUuids'])?$formData['dutyUuids']:''
])->label(false)?>
    <table class="table public-customer customer">
        <tbody>
        <tr>
            <td class="col-md-1">
                编号
            </td>
            <td class="col-md-3">
                <input
                    type="hidden"
                    name="PublicCustomerForm[code]"
                    value="<?= isset($formData['code'])?$formData['code']:''?>"
                >
                <?= $form->field($model,'code')->
                textInput([
                    'disabled'=>true,
                    
                    'value'=>isset($formData['code'])?
                        PublicCustomerForm::codePrefix.$formData['code']:
                        '',
                ])->
                label(false)?>
            </td>
            <td class="col-md-1"><?= $viewHelper->isRequiredFiled('name')?>*简称</td>
            <td class="col-md-3">
                <?= $form->field($model,'name')->
                textInput([
                    'class'=>'enableEdit form-control',
                    'disabled'=>$show,
                    
                    'value'=>isset($formData['name'])?$formData['name']:'',
                    'data-parsley-required'=>'true',
                ])->
                label(false)?>
            </td>
            <td class="col-md-1"><?= $viewHelper->isRequiredFiled('full_name')?>全称</td>
            <td class="col-md-3">
                <?= $form->field($model,'full_name')->
                textInput([
                    'class'=>'enableEdit form-control',
                    'disabled'=>$show,
                    'id'=>'full_name',
                    
                    'value'=>isset($formData['full_name'])?$formData['full_name']:'',
                    'data-parsley-required'=>'true',
                ])->
                label(false)?>
            </td>

        </tr>
        <tr>
            <td>类别</td>
            <td>
                <?= $form->field($model, 'type')->
                dropDownList($model->typeList(),
                    [
                        'class'=>'enableEdit form-control',
                        'disabled'=>$show,
                        'options'=>ViewHelper::defaultValueForDropDownList($show, $formData,'type'),
                    ])->
                label(false)?>
            </td>

            <td>行业</td>
            <td>
                <?= $form->field($model, 'industry')->
                dropDownList($model->industryList(),
                    [
                        'class'=>'enableEdit form-control',
                        'disabled'=>$show,
                        'options'=>ViewHelper::defaultValueForDropDownList($show, $formData,'industry'),
                    ])->
                label(false)?>
            </td>
            <td>城市</td>
            <td>
                <?= $form->field($model, 'city')->
                dropDownList($model->getList('city'),
                    [
                        'class'=>'enableEdit form-control',
                        'disabled'=>$show,
                        'options'=>ViewHelper::defaultValueForDropDownList($show, $formData,'city'),
                    ])->
                label(false)?>
            </td>
        </tr>
        <tr>
            <td>状态</td>
            <td>
                <?= $form->field($model, 'status')->
                dropDownList($config->getList('status'),
                    ['class'=>'form-control',
                        'options'=>ViewHelper::defaultValueForDropDownList($show, $formData,'status'),
                    ])->
                label(false)?>
            </td>
            <td>推荐级别</td>
            <td>
                <?= $form->field($model, 'intent_level')->
                dropDownList($model->intentLevelList(),
                    [
                        'class'=>'enableEdit form-control',
                        'disabled'=>$show,
                        'options'=>ViewHelper::defaultValueForDropDownList($show, $formData,'intent_level'),
                    ])->
                label(false)?>
            </td>
            <td>来源</td>
            <td>
                <?= $form->field($model, 'from')->
                dropDownList($model->fromList(),
                    [
                        'class'=>'enableEdit form-control',
                        'disabled'=>$show,
                        'options'=>ViewHelper::defaultValueForDropDownList($show, $formData,'from'),
                    ])->
                label(false)?>
            </td>
        </tr>
        <tr>
            <td>
                联系人
            </td>
            <td>

                <button type="button" <?= $show?'disabled':''?> class="enableEdit btn showContactList col-md-12">
                    联系人列表
                </button>
            </td>
            <td>
                业务板块
            </td>
            <td colspan="3">
                <?php $businessList = $model->getList('business');?>
                <?php if(!empty($businessList)) :?>
                <?php foreach($businessList as $key => $business) :?>
                    <div style="width: 100px; float: left">
                        <input name="PublicCustomerForm[business][]"
                               class = 'enableEdit' <?= $show?'disabled':''?>
                               type="checkbox" value="<?= $key?>" <?= BusinessModel::checkIdInList($requireList, $key)?'checked':''?> >
                        <?= $business?>
                    </div>
                <?php endforeach?>
                <?php endif?>
            </td>
        </tr>
        <tr>
            <td>网站</td>
            <td>
                <?= $form->field($model,'website')->
                textInput([
                    'class'=>'enableEdit form-control',
                    'disabled'=>$show,
                    
                    'data-parsley-type'=>"url",
                    'value'=>isset($formData['website'])?$formData['website']:''
                ])->label(false)?>
            </td>
            <td>地址</td>
            <td colspan="3">
                <?= $form->field($model,'address')->
                textInput([
                    'class'=>'enableEdit form-control',
                    'disabled'=>$show,
                    
                    'value'=>isset($formData['address'])?$formData['address']:''
                ])->label(false)?>
            </td>
        </tr>
        <tr>
            <td>简介</td>
            <td colspan="5">
                <?= $form->field($model,'description')->
                textarea([
                    'class'=>'enableEdit form-control',
                    'disabled'=>$show,
                    
                    'rows'=>3,
                    'value'=>isset($formData['description'])?$formData['description']:''
                ])->
                label(false)?>
            </td>
        </tr>
        <tr>
            <td>销售策略</td>
            <td colspan="5">
                <?= $form->field($model,'require_analyse')->
                textarea([
                    'class'=>'enableEdit form-control',
                    'disabled'=>$show,
                    
                    'rows'=>3,
                    'value'=>isset($formData['require_analyse'])?$formData['require_analyse']:''
                ])->
                label(false)?>
            </td>
        </tr>
        <tr>
            <td>备注</td>
            <td colspan="5">
                <?= $form->field($model,'remarks')->
                textarea([
                    'class'=>'enableEdit form-control',
                    'disabled'=>$show,
                    
                    'rows'=>3,
                    'value'=>isset($formData['remarks'])?$formData['remarks']:''
                ])->
                label(false)?>
            </td>
        </tr>
        <?php if(isset($formData['drop_reason']) && !empty($formData['drop_reason'])) :?>
            <tr>
                <td>放弃原因</td>
                <td colspan="5">
                    <?= $form->field($model,'drop_reason')->
                    textarea([
                        'class'=>'form-control',
                        'disabled'=>true,
                        'rows'=>3,
                        'value'=>$formData['drop_reason']
                    ])->
                    label(false)?>
                </td>
            </tr>
        <?php endif;?>
        <tr>
            <td></td><td></td><td></td><td></td>
            <td colspan="2">
                <button type="submit" class="btn btn-primary col-md-12 enableEdit" <?= $show?'disabled':''?>>提交</button>
            </td>
        </tr>
        </tbody>
    </table>
<?php ActiveForm::end();?>
<!--联系人面板-->
<?= $this->render('/contact/contact-panel',[
    'contactList'=>isset($contactList['contactList'])?$contactList['contactList']:'',
    'customerDutyList'=>isset($contactList['customerDutyList'])?$contactList['customerDutyList']:'',
    'model'=>$contactModel,
    'uuid'=>isset($formData['uuid'])?$formData['uuid']:'',
]);?>
