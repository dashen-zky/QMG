<?php
use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use backend\modules\rbac\model\RoleManager;
?>
<div class="form-container">
<?php $form = ActiveForm::begin([
    'options'=>[
        'enctype'=>'multipart/form-data',
        'class' => 'form-horizontal CandidateForm',
        'data-parsley-validate' => "true",
    ],
    'method' => 'post',
    'action' => $action,
    'fieldConfig' => [
        'template' => "{label}<div class=\"col-md-12\">{input}</div><div class=\"col-md-12\">{error}</div>",
        'labelOptions' => ['class' => 'col-md-3 control-label'],
    ],
])?>
<?php if (isset($edit) && $edit) :?>
    <div>
        <a href="javascript:;" class="editForm"
           style="font-size: 15px; float: right; margin-right: 30px;"><i class="fa fa-2x fa-pencil"></i>编辑</a>
    </div>
<?php endif;?>
<input hidden name="CandidateForm[candidate_uuid]"
       value="<?= isset($formData['candidate_uuid'])?$formData['candidate_uuid']:$formData['uuid']?>">
<input hidden name="CandidateForm[recruit_uuid]"
       value="<?= isset($formData['recruit_uuid'])?$formData['recruit_uuid']:''?>">
<table class="table">
    <tbody>
    <tr>
        <td class="col-md-1">姓名*</td>
        <td class="col-md-3">
            <?= Html::textInput('CandidateForm[name]',
                isset($formData['name'])?$formData['name']:null, [
                    'class'=>'form-control ',
                    'data-parsley-required'=>true,
                    'disabled'=>true,
                ])?>
        </td>
        <td class="col-md-1">职位*</td>
        <td class="col-md-3">
            <?= Html::textInput('CandidateForm[position]',
                isset($formData['position'])?$formData['position']:null, [
                    'class'=>'form-control ',
                    'data-parsley-required'=>true,
                    'disabled'=>true,
                ])?>
        </td>
        <td class="col-md-1">电话*</td>
        <td class="col-md-3">
            <?= Html::textInput('CandidateForm[phone]',
                isset($formData['phone'])?$formData['phone']:null, [
                    'class'=>'form-control phone ',
                    'data-parsley-required'=>true,
                    'data-parsley-type' => "number",
                    'disabled'=>true,
                ])?>
        </td>
    </tr>
    <tr>
        <td>邮箱</td>
        <td>
            <?= Html::textInput('CandidateForm[email]',
                isset($formData['email'])?$formData['email']:null, [
                    'class'=>'form-control ',
                    'disabled'=>true,
                ])?>
        </td>
        <td>期望薪资</td>
        <td>
            <?= Html::textInput('CandidateForm[expect_salary]',
                isset($formData['expect_salary'])?$formData['expect_salary']:null, [
                    'class'=>'form-control enableEdit',
                    'disabled'=>true,
                    'data-parsley-type' => "number",
                ])?>
        </td>
        <td>面试时间</td>
        <td>
            <?= Html::textInput('CandidateForm[interview_time]',
                (isset($formData['interview_time']) && $formData['interview_time'] != 0)?
                    date('Y-m-d H:i',$formData['interview_time']):null, [
                    'class'=>'input-section datetimepicker form-control enableEdit',
                    'disabled'=>true,
                ])?>
        </td>
    </tr>
    <tr>
        <td>可入职时间</td>
        <td>
            <?= Html::textInput('CandidateForm[entry_time]',
                (isset($formData['entry_time']) && $formData['entry_time'] != 0)?
                    date('Y-m-d H:i',$formData['entry_time']):null, [
                    'class'=>'input-section datetimepicker form-control enableEdit',
                    'disabled'=>true,
                ])?>
        </td>
        <td colspan="4"></td>
    </tr>
    <tr>
        <td>备注</td>
        <td colspan="5">
            <?= Html::textarea('CandidateForm[remarks]',
                isset($formData['remarks'])?$formData['remarks']:null, [
                    'class'=>'form-control ',
                    'disabled'=>true,
                    'rows'=>3
                ])?>
        </td>
    </tr>
    <tr>
        <td>用人单位建议</td>
        <td colspan="5">
            <?= Html::textarea('CandidateForm[demand_comment]',
                isset($formData['demand_comment'])?$formData['demand_comment']:null, [
                    'class'=>'form-control enableEdit',
                    'disabled'=>true,
                    'rows'=>3
                ])?>
        </td>
    </tr>
    <tr>
        <td>人事建议</td>
        <td colspan="5">
            <?= Html::textarea('CandidateForm[hr_comment]',
                isset($formData['hr_comment'])?$formData['hr_comment']:null, [
                    'class'=>'form-control',
                    'disabled'=>true,
                    'rows'=>3
                ])?>
        </td>
    </tr>
    <tr>
        <td>公司领导建议</td>
        <td colspan="5">
            <?php
            $roles = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->getIdentity()->getId()));
            $enableEdit = '';
            if(array_intersect($roles, [
                RoleManager::ceo,
                RoleManager::VicePresident,
            ])) {
                $enableEdit = 'enableEdit';
            } ?>
            <?= Html::textarea('CandidateForm[leader_comment]',
                isset($formData['leader_comment'])?$formData['leader_comment']:null, [
                    'class'=>'form-control ' . $enableEdit,
                    'disabled'=>true,
                    'rows'=>3
                ])?>
        </td>
    </tr>
    <tr>
        <td>
            简历*
        </td>
        <td colspan="4">
            <?php if(isset($formData['resume']) && !empty($formData['resume'])) :?>
                <?php
                // 将attachment字段解析出来
                $formData['resume'] = unserialize($formData['resume']);
                ?>
                <?php foreach($formData['resume'] as $key=>$path) :?>
                    <div>
                        <a href="<?= Url::to([
                            '/recruitment/candidate/resume-download',
                            'path'=>$path,
                            'file_name'=>$key,
                        ])?>"><?= $key?></a>
                    </div>
                <?php endforeach?>
            <?php endif?>
        </td>
        <td></td>
    </tr>
    </tbody>
</table>
<span class="col-md-12">
<span class="col-md-4"></span>
<span class="col-md-4">
<input type="submit"
       value="提交" style="display: none"
       class="form-control btn-primary displayBlockWhileEdit">
</span>
<span class="col-md-4"></span>
</span>
<?php ActiveForm::end()?>
</div>