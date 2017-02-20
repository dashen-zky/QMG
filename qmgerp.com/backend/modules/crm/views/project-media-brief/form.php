<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use backend\modules\crm\models\project\record\ProjectMediaBriefConfig;
?>
<?php $form = ActiveForm::begin([
    'options'=>[
        'enctype'=>'multipart/form-data',
        'class' => 'form-horizontal ProjectMediaBriefForm',
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
<input value="<?= $formData['project_uuid']?>" hidden name="ProjectMediaBriefForm[project_uuid]">
<input value="<?= isset($formData['uuid'])?$formData['uuid'] : ''?>" hidden name="ProjectMediaBriefForm[uuid]">
<table class="table">
    <tr>
        <td class="col-md-1">标题*</td>
        <td class="col-md-3">
            <?= Html::textInput('ProjectMediaBriefForm[title]',
                isset($formData['title'])?$formData['title']:null,
                [
                    'disabled'=>isset($show)&&$show,
                    'class'=>'form-control enableEdit',
                    'data-parsley-required'=>'true',
                ])?>
        </td>
        <td class="col-md-1">申请日期</td>
        <td class="col-md-3">
            <?= Html::textInput("ProjectMediaBriefForm[created_time]",
                isset($formData['created_time']) &&
                $formData['created_time'] != 0 ?date("Y-m-d",$formData['created_time']):null,[
                    'class'=>'form-control input-section datetimepicker',
                    'disabled'=>true,
                ])?>
        </td>
        <td class="col-md-1">编号</td>
        <td class="col-md-3">
            <?= Html::textInput('ProjectMediaBriefForm[id]',
                isset($formData['id'])?$formData['id']:null,
                [
                    'disabled'=>true,
                    'class'=>'form-control',
                ])?>
        </td>
    </tr>
    <tr>
        <td>状态</td>
        <td>
            <?= Html::dropDownList('ProjectMediaBriefForm[status]',
                isset($formData['status'])?$formData['status']:null,
                ProjectMediaBriefConfig::getList('status'), [
                    'class'=>'form-control',
                    'disabled'=>true,
                ])?>
        </td>
        <td>客户</td>
        <td>
            <?= Html::textInput('ProjectMediaBriefForm[customer_name]',
                isset($formData['customer_name'])?$formData['customer_name']:null,
                [
                    'disabled'=>true,
                    'class'=>'form-control',
                ])?>
        </td>
        <td>项目</td>
        <td>
            <?= Html::textInput('ProjectMediaBriefForm[project_name]',
                isset($formData['project_name'])?$formData['project_name']:null,
                [
                    'disabled'=>true,
                    'class'=>'form-control',
                ])?>
        </td>
    </tr>
    <tr>
        <td>销售</td>
        <td>
            <?= Html::textInput('ProjectMediaBriefForm[customer_name]',
                isset($formData['customer_name'])?$formData['customer_name']:null,
                [
                    'disabled'=>true,
                    'class'=>'form-control',
                ])?>
        </td>
        <td>项目经理</td>
        <td>
            <?= Html::textInput('ProjectMediaBriefForm[project_manager_name]',
                isset($formData['project_manager_name'])?$formData['project_manager_name']:null,
                [
                    'disabled'=>true,
                    'class'=>'form-control',
                ])?>
        </td>
        <td>项目成员</td>
        <td>
            <?= Html::textInput('ProjectMediaBriefForm[project_member_name]',
                isset($formData['project_member_name'])?$formData['project_member_name']:null,
                [
                    'disabled'=>true,
                    'class'=>'form-control',
                ])?>
        </td>
    </tr>
    <tr>
        <td>备注</td>
        <td colspan="5">
            <?= Html::textarea('ProjectMediaBriefForm[remarks]',
                isset($formData['remarks'])?$formData['remarks']:null,
                [
                    'disabled'=>isset($show)&&$show,
                    'class'=>'form-control enableEdit',
                    'rows'=>'3'
                ])?>
        </td>
    </tr>
    <?php if(isset($formData['refuse_reason']) && !empty($formData['refuse_reason'])) :?>
        <tr>
            <td>审核不同原因</td>
            <td colspan="5">
                <?= Html::textarea('ProjectMediaBriefForm[refuse_reason]',
                    isset($formData['refuse_reason'])?$formData['refuse_reason']:null,
                    [
                        'disabled'=>true,
                        'class'=>'form-control',
                        'rows'=>'3'
                    ])?>
            </td>
        </tr>
    <?php endif;?>
    <tr>
        <td>
            附件
        </td>
        <td>
            <?= $form->field(new \backend\models\UploadForm([
                'fileRules'=>[
                    'maxFiles' => 10,
                ]
            ]),'file[]')->fileInput([
                'multiple' => true,
            ])?>
        </td>
        <td colspan="4">
            <?php if(isset($formData['path']) && !empty($formData['path'])) :?>
                <?php
                // 将path字段解析出来
                $formData['path'] = unserialize($formData['path']);
                ?>
                <?php foreach($formData['path'] as $key=>$path) :?>
                    <div>
                        <a href="<?= Url::to([
                            '/crm/project-media-brief/attachment-download',
                            'path'=>$path,
                            'file_name'=>$key,
                        ])?>"><?= $key?></a>
                        <span class="displayBlockWhileEdit" style="float: right; display: <?= isset($show) && $show ? 'none':'' ?>">
                            <a href="#" url="<?= Url::to([
                                '/crm/project-media-brief/attachment-delete',
                                'path'=>$path,
                                'uuid'=>$formData['uuid'],
                            ])?>" class="attachmentDelete">删除</a>
                        </span>
                    </div>
                <?php endforeach?>
            <?php endif?>
        </td>
    </tr>
    <tr>
        <td colspan="6"></td>
    </tr>
</table>
<span class="col-md-12">
<span class="col-md-4"></span>
<span class="col-md-4">
    <input type="submit"
           value="提交" style="display: <?= isset($show) && $show ? 'none':'' ?>"
           class="form-control btn-primary displayBlockWhileEdit">
</span>
<span class="col-md-4"></span>
</span>
<?php ActiveForm::end()?>
