<?php
use yii\helpers\Html;
use backend\modules\hr\models\config\EmployeeBasicConfig;
$config = new EmployeeBasicConfig();
?>
<?= Html::beginForm($action, 'post', [
    'class' => 'form-horizontal AskLeaveForm',
    'data-parsley-validate' => "true",
])?>
<?php if (isset($edit) && $edit) :?>
    <div>
        <a href="javascript:;" class="editForm"
           style="font-size: 15px; float: right; margin-right: 30px;"><i class="fa fa-2x fa-pencil"></i>编辑</a>
    </div>
<?php endif;?>
<input value="<?= isset($formData['uuid'])?$formData['uuid'] : ''?>" hidden name="AskLeaveForm[uuid]">
<table class="table">
    <tr>
        <td>姓名*</td>
        <td>
            <?= Html::textInput('AskLeaveForm[name]',
                isset($formData['applied_name'])?$formData['applied_name'] : Yii::$app->getUser()->getIdentity()->getEmployeeName(),
                [
                    'disabled'=>"disabled",
                    'class'=>'form-control',
                    'data-parsley-required'=>'true',
                ])?>
        </td>
        <td>部门*</td>
        <td>
            <?= Html::textInput('AskLeaveForm[department]',
                isset($formData['department'])?$formData['department']:null,
                [
                    'disabled'=>isset($show)&&$show,
                    'class'=>'form-control enableEdit',
                    'data-parsley-required'=>'true',
                ])?>
        </td>
        <td>类型*</td>
        <td>
            <?php
            $typeList = $config->getList('ask_leave_type');
            foreach ($typeList as $index => $item) :?>
                <div class="col-md-4" style="float: left">
                    <input type="radio" class="enableEdit"
                        <?= isset($show) && $show ? 'disabled':''?>
                           <?= isset($formData['type']) && ($index == $formData['type'])?'checked':''?>
                           value="<?= $index?>" name="AskLeaveForm[type]">
                    <?= $item?>
                </div>
            <?php endforeach;?>
        </td>
    </tr>
    <tr>
        <td>开始时间*</td>
        <td>
            <?= Html::textInput("AskLeaveForm[start_time]",
                isset($formData['start_time']) &&
                $formData['start_time'] != 0 ?date("Y-m-d H:i:s",$formData['start_time']):null,[
                    'class'=>'form-control input-section datetimepicker enableEdit',
                    'disabled'=>isset($show)&&$show,
                    'data-parsley-required'=>'true',
                ])?>
        </td>
        <td>结束时间*</td>
        <td>
            <?= Html::textInput("AskLeaveForm[end_time]",
                isset($formData['end_time']) &&
                $formData['end_time'] != 0 ?date("Y-m-d H:i:s",$formData['end_time']):null,[
                    'class'=>'form-control input-section datetimepicker enableEdit',
                    'disabled'=>isset($show)&&$show,
                    'data-parsley-required'=>'true',
                ])?>
        </td>
        <td>共计(单位天)*</td>
        <td>
            <?= Html::textInput("AskLeaveForm[total_time]",
                isset($formData['total_time'])?$formData['total_time']:null,[
                    'class'=>'form-control enableEdit',
                    'disabled'=>isset($show)&&$show,
                    'data-parsley-required'=>'true',
                    'data-parsley-type'=>'number',
                    'placeholder'=>'必须为数字'
                ])?>
        </td>
    </tr>
    <tr>
        <td>职务代理人</td>
        <td>
            <?= Html::textInput("AskLeaveForm[proxy]",
                isset($formData['proxy'])?$formData['proxy']:null,[
                    'class'=>'form-control enableEdit',
                    'disabled'=>isset($show)&&$show,
                ])?>
        </td>
        <td>审核人</td>
        <td>
            <?= Html::textInput("AskLeaveForm[assess_name]",
                isset($formData['assess_name'])?$formData['assess_name']:null,[
                    'class'=>'form-control',
                    'disabled'=>true,
                ])?>
        </td>
        <td>请假状态</td>
        <td>
            <?= Html::dropDownList("AskLeaveForm[status]",
                isset($formData['status'])?$formData['status']:null,
                $config->getList('ask_for_leave_status'),
                [
                    'class'=>'form-control',
                    'disabled'=>true,
                ])?>
        </td>
    </tr>
    <tr>
        <td>事由</td>
        <td colspan="5">
            <?= Html::textInput("AskLeaveForm[reason]",
                isset($formData['reason'])?$formData['reason']:null,[
                    'class'=>'form-control enableEdit',
                    'disabled'=>isset($show)&&$show,
                    'data-parsley-required'=>'true',
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
    <input type="submit"
           value="提交" style="display: <?= isset($show) && $show ? 'none':'' ?>"
           class="form-control btn-primary displayBlockWhileEdit">
</span>
<span class="col-md-4"></span>
</span>
<?= Html::endForm()?>
<?php
$JS = <<<JS
//  时间插件
$(function () {
  $(".datetimepicker").datetimepicker({
    lang:"ch",           //语言选择中文
    format:"Y-m-d H:i",      //格式化日期H:i
    timepicker:true,
    i18n:{
      // 以中文显示月份
      de:{
        months:["1月","2月","3月","4月","5月","6月","7月","8月","9月","10月","11月","12月",],
        // 以中文显示每周（必须按此顺序，否则日期出错）
        dayOfWeek:["日","一","二","三","四","五","六"]
      }
    }
    // 显示成年月日，时间--
  });
  $('.search').click(function () {
    var star = $('input[name="iStarTime"]').val();
    var over = $('input[name="iOverTime"]').val();
    var iss = $('.iSource').val();
    var iss_a = $('.iSource_a').val();
//            四个值
    location.href = "/acenter/recharge/iss/"+iss+"/star/" + star + "/over/" + over + ".html";
  })
})
//  时间插件-end
JS;
$this->registerJs($JS, \yii\web\View::POS_END);
?>
