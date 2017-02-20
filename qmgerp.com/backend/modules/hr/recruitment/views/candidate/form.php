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
    <input hidden name="CandidateForm[uuid]" class="uuid" value="<?= isset($formData['uuid'])?$formData['uuid']:''?>">
    <table class="table">
        <tbody>
        <tr>
            <td class="col-md-1">姓名*</td>
            <td class="col-md-3">
                <?= Html::textInput('CandidateForm[name]',
                    isset($formData['name'])?$formData['name']:null, [
                        'class'=>'form-control enableEdit',
                        'data-parsley-required'=>true,
                        'disabled'=>isset($show)&&$show,
                    ])?>
            </td>
            <td class="col-md-1">职位*</td>
            <td class="col-md-3">
                <?= Html::textInput('CandidateForm[position]',
                    isset($formData['position'])?$formData['position']:null, [
                        'class'=>'form-control enableEdit',
                        'data-parsley-required'=>true,
                        'disabled'=>isset($show)&&$show,
                    ])?>
            </td>
            <td class="col-md-1">电话*</td>
            <td class="col-md-3">
                <?= Html::textInput('CandidateForm[phone]',
                    isset($formData['phone'])?$formData['phone']:null, [
                        'class'=>'form-control phone enableEdit',
                        'data-parsley-required'=>true,
                        'data-parsley-type' => "number",
                        'disabled'=>isset($show)&&$show,
                    ])?>
                <div class='phone-error' style="display: none; color: red">
                    该电话已经在存在，请检查是否重复录入
                </div>
            </td>
        </tr>
        <tr>
            <td>邮箱</td>
            <td>
                <?= Html::textInput('CandidateForm[email]',
                    isset($formData['email'])?$formData['email']:null, [
                        'class'=>'form-control enableEdit',
                        'disabled'=>isset($show)&&$show,
                    ])?>
            </td>
            <td colspan="4"></td>
        </tr>
        <tr>
            <td>备注</td>
            <td colspan="5">
                <?= Html::textarea('CandidateForm[remarks]',
                    isset($formData['remarks'])?$formData['remarks']:null, [
                        'class'=>'form-control enableEdit',
                        'disabled'=>isset($show)&&$show,
                        'rows'=>3
                    ])?>
            </td>
        </tr>
        <tr>
            <td>
                简历*
            </td>
            <td>
                <?= $form->field(new \backend\models\UploadForm([
                    'fileRules'=>[
                        'maxFiles' => 10,
                    ]
                ]),'file[]')->fileInput([
                    'multiple' => true,
                    'class'=>'enableEdit',
                    'disabled'=>isset($show)&&$show,
                    'data-parsley-required'=>'true',
                ])?>
            </td>
            <td colspan="3">
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
                            <span class="displayBlockWhileEdit" style="float: right; display: <?= !isset($show) || !$show?'block':'none'?>">
                            <a href="#" url="<?= Url::to([
                                '/recruitment/candidate/resume-delete',
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
        </tbody>
    </table>
<span class="col-md-12">
<span class="col-md-4"></span>
<span class="col-md-4">
<input type="button"
       url = "<?= Url::to([
           '/recruitment/candidate/validate-phone'
       ])?>"
       value="提交" style="display: <?= isset($show) && $show ? 'none':'' ?>"
       class="submit form-control btn-primary displayBlockWhileEdit">
</span>
<span class="col-md-4"></span>
</span>
<?php ActiveForm::end()?>
</div>
<?php
$Js = <<<JS
$('.CandidateForm').on('click', '.submit', function() {
    var form = $(this).parents('.CandidateForm');
    var url = $(this).attr('url') + '&phone=' + form.find('.phone').val();
    $.get(url, function(data, status) {
        if ('success' !== status) {
            return ;
        }
        
        var uuid = form.find('.uuid').val();
        if (data != 1 && data !== uuid) {
            form.find('.phone-error').css('display','block');
            return false;
        }
        
        form.submit();
    });
})
JS;
$this->registerJs($Js, \yii\web\View::POS_END);
?>