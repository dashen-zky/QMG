<?php
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use backend\models\ViewHelper;
use backend\modules\crm\models\supplier\model\SupplierForm;
use yii\helpers\Json;
use backend\modules\rbac\model\PermissionManager;
use backend\modules\rbac\model\RoleManager;
use backend\modules\hr\models\EmployeeBasicInformation;
use backend\modules\crm\models\supplier\record\Supplier;
$viewHelper = new ViewHelper($model);
?>
<?php $form = ActiveForm::begin([
    'options'=>[
        'enctype'=>'multipart/form-data',
        'class' => 'form-horizontal ' . $formClass,
        'data-parsley-validate' => "true",
    ],
    'method' => 'post',
    'action' => $action,
    'fieldConfig' => [
        'template' => "{label}<div class=\"col-md-12\">{input}</div><div class=\"col-md-12\">{error}</div>",
        'labelOptions' => ['class' => 'col-md-3 control-label'],
    ],
])?>
<!--// 如果没有设置enableEdit,那么只要show就可以了，如果有设置enableEdit,那么我们要求enableEdit必须为true-->
<!--    // 查看是否有编辑的权限-->
<?php if(($show && !isset($enableEdit) && (isset($enableEditSupplier) && $enableEditSupplier)) || ($show && isset($enableEdit) && $enableEdit)):?>
    <div style="float: right">
        <a href="javascript:;" class="editForm" style="font-size: 15px"><i class="fa fa-2x fa-pencil"></i>编辑</a>
    </div>
<?php endif?>
    <input type="hidden" name="SupplierForm[uuid]" value="<?=
    isset($supplier['uuid'])?$supplier['uuid']:''
    ?>">
<?= $form->field($model,'contactUuids')->
input('hidden',[
    'class'=>'contactUuids',
    'value'=>isset($formData['contactUuids'])?$formData['contactUuids']:''
])->label(false)?>
<input hidden
       value='<?= (isset($backUrl) && !empty($backUrl))?$backUrl:Json::encode(["/crm/supplier/index"])?>'
       name="backUrl">
    <table class="table supplier-table">
        <tbody>
        <tr>
            <td class="col-md-1">供应商编码</td>
            <td class="col-md-3">
                <input type="hidden" name="SupplierForm[code]" value="<?= isset($supplier['code'])?$supplier['code']:''?>">
                <input name="xx"
                       class="form-control"
                       disabled
                       value="<?= isset($supplier['code'])?SupplierForm::codePrefix.$supplier['code']:''?>">
            </td>
            <td class="col-md-1"><?= $viewHelper->isRequiredFiled('name')?>供应商名字</td>
            <td class="col-md-3">
                <?= $form->field($model,'name')->textInput([
                    'data-parsley-required'=>'true',
                    'class'=>'enableEdit form-control',
                    'disabled'=>$show,
                    'value'=>isset($supplier['name'])?$supplier['name']:''
                ])->label(false)?>
            </td>
            <td class="col-md-1">级别</td>
            <td class="col-md-3">
                <?= $form->field($model, 'level')->
                dropDownList($model->config->getList('level'),[
                    'class'=>'enableEdit form-control',
                    'disabled'=>$show,
                    'options'=>ViewHelper::defaultValueForDropDownList(true, $supplier,'level'),
                ])->
                label(false)?>
            </td>
        </tr>
        <tr>
            <td>性质</td>
            <td>
                <?= $form->field($model, 'feature')->
                dropDownList($model->config->getList('feature'),[
                    'class'=>'enableEdit form-control',
                    'disabled'=>$show,
                    'options'=>ViewHelper::defaultValueForDropDownList(true, $supplier,'feature'),
                ])->
                label(false)?>
            </td>
            <td>类型</td>
            <td>
                <?= $form->field($model, 'type')->
                dropDownList($model->config->getList('type'),[
                    'class'=>'enableEdit form-control',
                    'disabled'=>$show,
                    'options'=>ViewHelper::defaultValueForDropDownList(true, $supplier,'type'),
                ])->
                label(false)?>
            </td>
            <td>审核状态</td>
            <td>
                <?php
                    // SupplierAndPartTimeAccess 是错别单词应该是SupplierAndPartTimeAssess 审核的意思
                    $canAssess = Supplier::canAssess(isset($supplier['uuid'])?$supplier['uuid']:null);
                    $enableEdit = $canAssess ? ' enableEdit':'';
                ?>
                <?= $form->field($model, 'status')->
                dropDownList($model->config->getList('status'),[
                    'class'=>'form-control ' . $enableEdit,
                    'disabled'=>$show || !$canAssess,
                    'options'=>ViewHelper::defaultValueForDropDownList(true, $supplier,'status'),
                ])->
                label(false)?>
            </td>
        </tr>
        <tr>
            <td>来源</td>
            <td>
                <?= $form->field($model, 'from')->
                dropDownList($model->config->getList('from'),[
                    'class'=>'enableEdit form-control',
                    'disabled'=>$show,
                    'options'=>ViewHelper::defaultValueForDropDownList(true, $supplier,'from'),
                ])->
                label(false)?>
            </td>
            <td>价格有效期</td>
            <td>
                <?= $form->field($model, 'value_term')->
                dropDownList($model->config->getList('value_term'),[
                    'class'=>'enableEdit form-control',
                    'disabled'=>$show,
                    'options'=>ViewHelper::defaultValueForDropDownList(true, $supplier,'value_term'),
                ])->
                label(false)?>
            </td>
            <td>供应商账期</td>
            <td>
                <?= $form->field($model, 'term')->
                dropDownList(ViewHelper::appendElementOnDropDownList($model->config->getList('term')),[
                    'class'=>'form-control enableEdit',
                    'disabled'=>$show,
                    'options'=>ViewHelper::defaultValueForDropDownList(true, $supplier,'term'),
                ])->
                label(false)?>
            </td>
        </tr>
        <tr>
            <td><?= $viewHelper->isRequiredFiled('bottom_value')?>保底金额</td>
            <td>
                <?= $form->field($model,'bottom_value')->textInput([
                    'class'=>'enableEdit form-control',
                    'data-parsley-type'=>"number",
                    'disabled'=>$show,
                    'value'=>isset($supplier['bottom_value'])?$supplier['bottom_value']:''
                ])->label(false)?>
            </td>
            <td>管理者</td>
            <td>
                <?php
                // managerlist 就是媒介的成员的列表
                $uuids = Yii::$app->authManager->getUserIdsByRoles([
                    RoleManager::Media,
                    RoleManager::MediaManager,
                    RoleManager::MediaDirector,
                ]);
                $managerList = (new EmployeeBasicInformation())->getEmployeeListByUuidsForDropDown($uuids);
                ?>
                <?= $form->field($model, 'manager_uuid')->
                dropDownList(ViewHelper::appendElementOnDropDownList($managerList),[
                    'class'=>'form-control enableEdit',
                    'disabled'=>$show || !$canAssess,
                    'options'=>ViewHelper::defaultValueForDropDownList(true, $supplier,'manager_uuid'),
                ])->
                label(false)?>
            </td>
            <td>
                联系人
            </td>
            <td>
                <?php if((isset($enableEditSupplier) && $enableEditSupplier) || !isset($enableEditSupplier)) :?>
                <button type="button" <?= $show?'disabled':''?> class="btn showContactList col-md-12 enableEdit">
                    联系人列表
                </button>
                <?php endif?>
            </td>
        </tr>
        <tr>
            <td>简介</td>
            <td colspan="7">
                <?= $form->field($model,'description')->
                textarea([
                    'class'=>'enableEdit form-control',
                    'disabled'=>$show,
                    'rows'=>3,
                    'value'=>isset($supplier['description'])?$supplier['description']:''])->
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
                    'value'=>isset($supplier['remarks'])?$supplier['remarks']:''])->
                label(false)?>
            </td>
        </tr>
        <tr>
            <td>审核不通过原因</td>
            <td colspan="5">
                <?= $form->field($model,'refuse_reason')->
                textarea([
                    'class'=>'enableEdit form-control',
                    'disabled'=>$show,
                    'rows'=>3,
                    'value'=>isset($supplier['refuse_reason'])?$supplier['refuse_reason']:''])->
                label(false)?>
            </td>
        </tr>
        <tr>
            <td>附件</td>
            <td><?= $form->field($model,'attachment')->fileInput([
                    'class'=>'enableEdit',
                    'disabled'=>$show,
                ])?></td>
            <td colspan="2">
                <?php if(isset($supplier['path']) && !empty($supplier['path'])) :?>
                    <a href="<?= Url::to(['/crm/supplier/attachment-download',
                        'path'=>$supplier['path']])?>">下载合同附件</a>
                <?php endif?>
            </td>
            <td></td>
            <td></td>
        </tr>
        <tr><td colspan="6"></td></tr>
        </tbody>
    </table>
<span class="col-md-12">
    <span class="col-md-4"></span>
    <span class="col-md-4">
        <input type="submit" <?= $show?'disabled':''?> value="提交" class="form-control enableEdit btn-primary">
    </span>
    <span class="col-md-4"></span>
</span>
<?php ActiveForm::end()?>

<!--客户联系人面板-->
<?= $this->render('/contact/contact-panel',[
    'contactList'=>isset($contactList['contactList'])?$contactList['contactList']:'',
    'customerDutyList'=>isset($contactList['customerDutyList'])?$contactList['customerDutyList']:'',
    'model'=>$contactModel,
    'uuid'=>isset($supplier['uuid'])?$supplier['uuid']:'',
]);?>
