<?php
use yii\helpers\Html;
use backend\modules\hr\models\Position;
use yii\helpers\Url;
use backend\models\ViewHelper;
use backend\modules\hr\recruitment\models\ApplyRecruitConfig;

$config = new ApplyRecruitConfig();
$position = new Position();
?>
<div class="panel">
<?= Html::beginForm($action, 'post', [
    'class' => 'form-horizontal ApplyRecruitForm',
    'data-parsley-validate' => "true",
])?>
<?php if (isset($edit) && $edit) :?>
    <div>
        <a href="javascript:;" class="editForm"
           style="font-size: 15px; float: right; margin-right: 30px;"><i class="fa fa-2x fa-pencil"></i>编辑</a>
    </div>
<?php endif;?>
<input value="<?= isset($formData['uuid'])?$formData['uuid'] : ''?>" hidden name="ApplyRecruitForm[uuid]">
<table class="table">
    <tr>
        <td class="col-md-1">招聘人</td>
        <td class="col-md-3">
            <?= Html::textInput('ApplyRecruitForm[created_name]',
                isset($formData['created_name'])?$formData['created_name']:
                    Yii::$app->getUser()->getIdentity()->getEmployeeName(),[
                    'class'=>'form-control',
                    'disabled'=>true,
                ])?>
        </td>
        <td class="col-md-1">期望招聘人数*</td>
        <td class="col-md-3">
            <?= Html::textInput('ApplyRecruitForm[number_of_plan]',
                isset($formData['number_of_plan'])?$formData['number_of_plan']:null,[
                    'class'=>'form-control enableEdit number_of_plan',
                    'disabled'=>isset($show)&&$show,
                    'data-parsley-type'=>'number',
                    'data-parsley-required'=>'true',
                ])?>
            <div class='number_of_plan_error' style="display: none; color: red">
                期望招聘人数不可以为0或是大于最大可招聘人数
            </div>
        </td>
        <td class="col-md-1">已招聘人数*</td>
        <td class="col-md-3">
            <?= Html::textInput('ApplyRecruitForm[number_of_succeed]',
                isset($formData['number_of_succeed'])?$formData['number_of_succeed']:0,[
                    'class'=>'form-control',
                    'disabled'=>true,
                ])?>
        </td>
    </tr>
    <tr>
        <td class="col-md-1">审核人</td>
        <td class="col-md-3">
            <?= Html::textInput('ApplyRecruitForm[assess_name]',
                isset($formData['assess_name'])?$formData['assess_name']:null,[
                    'class'=>'form-control',
                    'disabled'=>true,
                ])?>
        </td>
        <td>状态</td>
        <td>
            <?= Html::dropDownList('ApplyRecruitForm[status]',
                isset($formData['status'])?$formData['status']:null,
                $config->getList('status'), [
                    'class'=>'form-control',
                    'disabled'=>true,
                ])?>
        </td>
        <td>创建时间</td>
        <td>
            <?= Html::textInput('ApplyRecruitForm[created_time]',
                (isset($formData['created_time']) && $formData['created_time'] != 0)?
                    date('Y-m-d',$formData['created_time']):null,[
                    'class'=>'form-control',
                    'disabled'=>true,
                ])?>
        </td>
    </tr>
    <tr>
        <td>招聘原因</td>
        <td>
            <?= Html::dropDownList('ApplyRecruitForm[rest_number]',
                isset($formData['reason'])?$formData['reason']:null,
                $config->getList('reason'),[
                    'disabled'=>isset($show)&&$show,
                    'class'=>'form-control enableEdit',
                ])?>
        </td>
        <td class="col-md-1">薪资建议</td>
        <td class="col-md-3">
            <?= Html::textInput('ApplyRecruitForm[salary]',
                isset($formData['salary'])?$formData['salary']:null,[
                    'class'=>'form-control enableEdit',
                    'disabled'=>isset($show)&&$show,
                ])?>
        </td>
        <td class="col-md-1">期望到岗时间</td>
        <td class="col-md-3">
            <?= Html::textInput('ApplyRecruitForm[entry_time]',
                (isset($formData['entry_time']) && $formData['entry_time'] != 0)?
                    date('Y-m-d',$formData['entry_time']):null,[
                    'class'=>'form-control enableEdit datetimepicker',
                    'disabled'=>isset($show)&&$show,
                ])?>
        </td>
    </tr>
    <tr>
        <td class="col-md-1">选择职位*</td>
        <td class="col-md-3">
            <?= Html::dropDownList('ApplyRecruitForm[position_uuid]',
                isset($formData['position_uuid'])?$formData['position_uuid']:null,
                ViewHelper::appendElementOnDropDownList($position->canRecruitPositionList()),
                [
                    'url'=>Url::to(['/recruitment/apply-recruit/load-position-information']),
                    'class'=>'form-control position_uuid',
                    'disabled'=>isset($show)&&$show,
                ])?>
            <div class='position_uuid_error' style="display: none; color: red">
                职位不可以为空
            </div>
        </td>
        <td>
            最多可招聘人数
        </td>
        <td>
            <?= Html::textInput('ApplyRecruitForm[rest_number]',
                isset($formData['rest_number'])?$formData['rest_number']:null,[
                    'class'=>'form-control rest_number',
                    'disabled'=>true,
                ])?>
        </td>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td>职位要求</td>
        <td colspan="5">
            <?= Html::textarea('ApplyRecruitForm[position_requirement]',
                isset($formData['position_requirement'])?$formData['position_requirement']:null,
                [
                    'disabled'=>true,
                    'class'=>'form-control position_requirement',
                    'rows'=>'3',
                ])?>
        </td>
    </tr>
    <tr>
        <td>招聘要求</td>
        <td colspan="5">
            <?= Html::textarea('ApplyRecruitForm[description]',
                isset($formData['description'])?$formData['description']:null,
                [
                    'class'=>'form-control enableEdit',
                    'disabled'=>isset($show)&&$show,
                    'rows'=>'3',
                ])?>
        </td>
    </tr>
    <tr>
        <td>备注</td>
        <td colspan="5">
            <?= Html::textarea('ApplyRecruitForm[remarks]',
                isset($formData['remarks'])?$formData['remarks']:null,
                [
                    'class'=>'form-control enableEdit',
                    'disabled'=>isset($show)&&$show,
                    'rows'=>'3',
                ])?>
        </td>
    </tr>
    <?php if(isset($formData['status']) && $formData['status'] == ApplyRecruitConfig::StatusAssessRefused) :?>
    <tr>
        <td>不通过原因</td>
        <td colspan="5">
            <?= Html::textarea('ApplyRecruitForm[refuse_reason]',
                isset($formData['refuse_reason'])?$formData['refuse_reason']:null,
                [
                    'class'=>'form-control',
                    'disabled'=>true,
                    'rows'=>'3',
                ])?>
        </td>
    </tr>
    <?php endif;?>
    <tr>
        <td colspan="6"></td>
    </tr>
</table>
<span class="col-md-12">
<span class="col-md-4"></span>
<span class="col-md-4">
    <input type="button"
           value="提交" style="display: <?= isset($show) && $show ? 'none':'' ?>"
           class="submit form-control btn-primary displayBlockWhileEdit">
</span>
<span class="col-md-4"></span>
</span>
<?= Html::endForm()?>
</div>
<?php 
$JS = <<<JS
$(function () {
    $('.ApplyRecruitForm').on('change','.position_uuid', function() {
        var url = $(this).attr('url') + '&position_uuid=' + $(this).val();
        var form = $(this).parents('form');
        $.get(
        url,
        function(data,status) {
            if('success' !== status) {
                return null;
            }
            
            data = JSON.parse(data);
            $.each(data, function(index, value) {
                form.find('.' + index).val(value);
            })
        });
    });
    
    $('.ApplyRecruitForm').on('click','.submit', function() {
        var form = $(this).parents('form');
        var number_of_plan = form.find('.number_of_plan').val();
        var rest_number = form.find('.rest_number').val();
        if (number_of_plan > rest_number || number_of_plan == 0) {
            form.find('.number_of_plan_error').css('display','block');
            return false;
        }
        
        var position_uuid = form.find('.position_uuid').val();
        if (position_uuid == 0) {
            form.find('.position_uuid_error').css('display','block');
            return false;
        }
        
        form.submit();
    });
})
JS;

$this->registerJs($JS, \yii\web\View::POS_END);
?>
