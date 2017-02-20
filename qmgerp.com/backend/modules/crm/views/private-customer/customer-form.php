<?php
use yii\widgets\ActiveForm;
use backend\modules\rbac\model\RoleManager;
use backend\models\ViewHelper;
use backend\modules\crm\models\customer\model\BusinessModel;
use backend\modules\crm\models\customer\model\PublicCustomerForm;
use backend\modules\hr\models\EmployeeBasicInformation;
use backend\modules\rbac\model\PermissionManager;
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
<?php if(!isset($enableEdit) || $enableEdit):?>
    <div style="float: right">
        <a href="javascript:;" class="editForm" style="font-size: 15px"><i class="fa fa-2x fa-pencil"></i>编辑</a>
    </div>
<?php endif?>
<?= $form->field($model,'uuid')->
input('hidden',[
    'value'=>isset($formData['uuid'])?$formData['uuid']:''
])->label(false)?>
<?= $form->field($model,'contactUuids')->
input('hidden',[
    'class'=>'contactUuids',
    'value'=>isset($formData['contactUuids'])?$formData['contactUuids']:''
])->label(false)?>
    <table class="table private-customer customer">
        <tbody>
        <tr>
            <td class="col-md-1">
                编号
            </td>
            <td class="col-md-3">
                <input
                    type="hidden"
                    name="PrivateCustomerForm[code]"
                    value="<?= isset($formData['code'])?$formData['code']:''?>"
                >
                <?= $form->field($model,'code1')->
                textInput([
                    'disabled'=>true,
                    
                    'value'=>isset($formData['code'])?
                        PublicCustomerForm::codePrefix.$formData['code']:
                        '',
                ])->
                label(false)?>
            </td>
            <td class="col-md-1">*简称</td>
            <td class="col-md-3">
                <?= $form->field($model,'name')->
                textInput([
                    
                    'value'=>isset($formData['name'])?$formData['name']:'',
                    'class'=>'enableEdit form-control',
                    'disabled'=>$show,
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
                    
                    'value'=>isset($formData['full_name'])?$formData['full_name']:'',
                    'data-parsley-required'=>'true',
                ])->
                label(false)?>
            </td>

        </tr>
        <tr>
            <td>状态</td>
            <td>
                <?= $form->field($model, 'status')->
                dropDownList($config->getList('status'),
                    [
                        'class'=>'enableEdit form-control',
                        'disabled'=>$show,
                        'options'=>ViewHelper::defaultValueForDropDownList($show, $formData,'status'),
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
            <td>级别</td>
            <td>
                <?= $form->field($model, 'level')->
                dropDownList($config->getList('level'),
                    [
                        'class'=>'enableEdit form-control',
                        'disabled'=>$show,
                        'options'=>ViewHelper::defaultValueForDropDownList($show, $formData,'level'),
                    ])->
                label(false)?>
            </td>
        </tr>
        <tr>
            <td>意向度</td>
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
            <td>指定销售</td>
            <td>
                <?php
                $uuids = Yii::$app->authManager->getUserIdsByRoles([
                    RoleManager::Sales,
                    RoleManager::SalesManager,
                    RoleManager::SalesDirector,
                    RoleManager::ceo
                ]);
                $employee = new EmployeeBasicInformation();
                $records = $employee->getEmployeeListByUuids($uuids);
                $employeeList = $employee->transformForDropDownList($records['employeeList'], 'uuid', 'name');
                ?>
                <?= $form->field($model, 'sales_uuid')->
                dropDownList($viewHelper->appendElementOnDropDownList($employeeList),
                    [
                        'class'=>'form-control ' . (Yii::$app->authManager->canAccess(
                            PermissionManager::DeleteCustomer
                        )?'enableEdit':''),
                        'disabled'=>$show,
                        'options'=>ViewHelper::defaultValueForDropDownList($show, $formData,'sales_uuid'),
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
            <td>
                业务板块
            </td>
            <td colspan="3">
                <?php $businessList = $model->getList('business');?>
                <?php if(!empty($businessList)) :?>
                    <?php foreach($businessList as $key => $business) :?>
                        <div style="width: 100px; float: left">
                            <input
                                <?= $show?'disabled':''?>
                                class="enableEdit"
                                name="PrivateCustomerForm[business][]"
                                type="checkbox" value="<?= $key?>"
                                <?= BusinessModel::checkIdInList($requireList, $key)?'checked':''?> >
                            <?= $business?>
                        </div>
                    <?php endforeach?>
                <?php endif?>
            </td>
        </tr>
        <tr>
            <td>
                联系人
            </td>
            <td>
                <button type="button" <?= $show?'disabled':''?> class="btn showContactList col-md-12 enableEdit">
                    联系人列表
                </button>
            </td>
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
        </tr>
        <tr>
            <td>
                最近跟进时间
            </td>
            <td>
                <?= $form->field($model,'last_touch_time')->
                textInput([
                    
                    'disabled'=>"true",
                    'value'=>isset($formData['last_touch_time'])?
                        (($formData['last_touch_time'] != 0)?date("Y-m-d",$formData['last_touch_time']):''):''
                ])->
                label(false)?>
            </td>
            <td>
                下次跟进时间
            </td>
            <td>
                <?= $form->field($model,'next_touch_time')->
                textInput([
                    
                    'disabled'=>"true",
                    'value'=>isset($formData['next_touch_time'])?
                        (($formData['next_touch_time'] != 0)?date("Y-m-d",$formData['next_touch_time']):''):''
                ])->
                label(false)?>
            </td>
            <td>地址</td>
            <td>
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
                <button type="submit" <?= $show?'disabled':''?> class="btn btn-primary col-md-12 enableEdit">提交</button>
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