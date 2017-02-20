<?php
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use backend\models\ViewHelper;
use backend\models\BaseForm;
use yii\helpers\Json;
use backend\modules\rbac\model\PermissionManager;
use backend\modules\rbac\model\RoleManager;
use backend\modules\hr\models\EmployeeBasicInformation;
use backend\modules\crm\models\part_time\record\PartTime;
$viewHelper = new ViewHelper($model);
?>
<?php $form = ActiveForm::begin([
    'options'=>[
        'enctype'=>'multipart/form-data',
        'class' => 'form-horizontal ' . $formClass,
        'data-parsley-validate' => "true",
    ],
    'method' => 'post',
    'enableClientValidation'=>true,
    'action' => $action,
    'fieldConfig' => [
        'template' => "{label}<div class=\"col-md-12\">{input}</div><div class=\"col-md-12\">{error}</div>",
        'labelOptions' => ['class' => 'col-md-3 control-label'],
    ],
])?>
<!--// 如果没有设置enableEdit,那么只要show就可以了，如果有设置enableEdit,那么我们要求enableEdit必须为true-->
<?php if(($show && !isset($enableEdit) && (isset($enableEditPartTime) && $enableEditPartTime)) || ($show && isset($enableEdit) && $enableEdit)):?>
    <div style="float: right">
        <a href="javascript:;" class="editForm" style="font-size: 15px"><i class="fa fa-2x fa-pencil"></i>编辑</a>
    </div>
<?php endif?>
    <input type="hidden" name="PartTimeForm[uuid]"
           value="<?= isset($partTime['uuid'])?$partTime['uuid']:''?>">
    <input hidden
       value='<?= (isset($backUrl) && !empty($backUrl))?$backUrl:Json::encode(["/crm/part-time/index"])?>'
       name="backUrl">
    <table class="table part-time-table">
        <tbody>
        <tr>
            <td class="col-md-1"><?= $viewHelper->isRequiredFiled('code')?>兼职编码</td>
            <td class="col-md-3">
                <input value="<?= $partTime['code']?>" type="hidden" name="PartTimeForm[code]">
                <?= $form->field($model,'code1')->textInput([
                    'disabled'=>true,
                    'value'=>\backend\modules\crm\models\part_time\model\PartTimeForm::codePrefix.$partTime['code']
                ])->label(false)?>
            </td>
            <td class="col-md-1"><?= $viewHelper->isRequiredFiled('name')?>名字</td>
            <td class="col-md-3">
                <?= $form->field($model,'name')->textInput([
                    'class'=>'enableEdit form-control',
                    'disabled'=>$show,
                    'data-parsley-required'=>'true',
                    'value'=>isset($partTime['name'])?$partTime['name']:''
                ])->label(false)?>
            </td>
            <td class="col-md-1">状态</td>
            <td class="col-md-3">
                <?= $form->field($model, 'status')->
                dropDownList($model->config->getList('status'),[
                    'class'=>'enableEdit form-control',
                    'disabled'=>$show,
                    'options'=>ViewHelper::defaultValueForDropDownList(true, $partTime, 'status'),
                ])->
                label(false)?>
            </td>
        </tr>
        <tr>
            <td>性别</td>
            <td>
                <?= $form->field($model, 'gender')->
                dropDownList(BaseForm::genderList(),[
                    'class'=>'enableEdit form-control',
                    'disabled'=>$show,
                    'options'=>ViewHelper::defaultValueForDropDownList(true, $partTime,'gender'),
                ])->
                label(false)?>
            </td>
            <td>职能</td>
            <td>
                <?= $form->field($model, 'position')->
                dropDownList($model->config->getList('position'),[
                    'class'=>'enableEdit form-control',
                    'disabled'=>$show,
                    'options'=>ViewHelper::defaultValueForDropDownList(true, $partTime,'position'),
                ])->
                label(false)?>
            </td>
            <td>审核状态</td>
            <td>
                <?php
                $canAccess = PartTime::canAssess(isset($partTime['uuid'])?$partTime['uuid']:null);
                $enableEdit = $canAccess ? ' enableEdit':'';
                ?>
                <?= $form->field($model, 'check_status')->
                dropDownList($model->config->getList('check_status'),[
                    'class'=>'form-control ' . $enableEdit,
                    'disabled'=>$show || !$canAccess,
                    'options'=>ViewHelper::defaultValueForDropDownList(true, $partTime,'check_status'),
                ])->
                label(false)?>
            </td>
        </tr>
        <tr>
            <td><?= $viewHelper->isRequiredFiled('english_name')?>英文名</td>
            <td>
                <?= $form->field($model,'english_name')->textInput([
                    'class'=>'enableEdit form-control',
                    'disabled'=>$show,
                    'value'=>isset($partTime['english_name'])?$partTime['english_name']:''
                ])->label(false)?>
            </td>
            <td><?= $viewHelper->isRequiredFiled('phone')?>电话</td>
            <td>
                <?= $form->field($model,'phone')->textInput([
                    'class'=>'enableEdit form-control',
                    'disabled'=>$show,
                    'data-parsley-required'=>'true',
                    'value'=>isset($partTime['phone'])?$partTime['phone']:''
                ])->label(false)?>
            </td>
            <td>qq</td>
            <td>
                <?= $form->field($model,'qq')->textInput([
                    'class'=>'enableEdit form-control',
                    'disabled'=>$show,
                    'value'=>isset($partTime['qq'])?$partTime['qq']:''
                ])->label(false)?>
            </td>
        </tr>
        <tr>
            <td>微信</td>
            <td>
                <?= $form->field($model,'wechat')->textInput([
                    'class'=>'enableEdit form-control',
                    'disabled'=>$show,
                    'value'=>isset($partTime['wechat'])?$partTime['wechat']:''
                ])->label(false)?>
            </td>
            <td>邮箱</td>
            <td>
                <?= $form->field($model,'email')->textInput([
                    'class'=>'enableEdit form-control',
                    'disabled'=>$show,
                    'data-parsley-type'=>"email",
                    'value'=>isset($partTime['email'])?$partTime['email']:''
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
                    'class'=>'form-control ' . $enableEdit,
                    'disabled'=>$show || !$canAccess,
                    'options'=>ViewHelper::defaultValueForDropDownList(true, $partTime,'manager_uuid'),
                ])->
                label(false)?>
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
                    'value'=>isset($partTime['description'])?$partTime['description']:''])->
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
                    'value'=>isset($partTime['remarks'])?$partTime['remarks']:''])->
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
                    'value'=>isset($partTime['refuse_reason'])?$partTime['refuse_reason']:''])->
                label(false)?>
            </td>
        </tr>
        <tr>
            <td>附件</td>
            <td><?= $form->field($model,'attachment[]')->fileInput([
                    'multiple' => true,
                    'class'=>'enableEdit',
                    'disabled'=>$show,
                ])?></td>
            <td colspan="4">
                <?php if(isset($partTime['path']) && !empty($partTime['path'])) :?>
                    <?php foreach($partTime['path'] as $key=>$path) :?>
                    <div>
                        <a href="<?= Url::to([
                                '/crm/part-time/attachment-download',
                                'path'=>$path,
                                'file_name'=>$key,
                            ])?>" class="enableEdit" <?= $show?'disabled':''?>><?= $key?></a>
                        <span style="float: right">
                            <a href="#" name="<?= Url::to([
                                '/crm/part-time/attachment-delete',
                                'path'=>$path,
                                'uuid'=>$partTime['uuid'],
                            ])?>" class="attachmentDelete enableEdit" <?= $show?'disabled':''?>>删除</a>
                        </span>
                    </div>
                    <?php endforeach?>
                <?php endif?>
            </td>
            <td></td>
            <td></td>
        </tr>
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
<?php
$JS = <<<JS
$(function() {
    // 附件删除功能
 $('.PartTimeForm .part-time-table').on('click','.attachmentDelete',function() {
    var url = $(this).attr('name');
    var self = $(this);
    $.get(
    url,
    function(data,status) {
        if('success' == status) {
            if(data) {
                self.parentsUntil('td').remove();
            }
        }
    });
 });
})
JS;
$this->registerJs($JS, \yii\web\View::POS_END);
?>
