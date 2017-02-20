<?php
use yii\helpers\Html;
use backend\modules\rbac\model\RoleManager;
use backend\modules\hr\models\EmployeeBasicInformation;
use backend\modules\statistic\models\SalesAnniversaryAchievementStatistic;
use backend\modules\rbac\model\RBACManager;
use backend\models\ViewHelper;
use yii\helpers\Url;
?>
<div class="form-container">
<?= Html::beginForm($action, 'post', [
    'class' => 'form-horizontal AnniversaryAchievementForm',
    'data-parsley-validate' => true,
])?>
<input hidden value="<?= isset($formData['uuid'])?$formData['uuid']:''?>" name="AnniversaryAchievementForm[uuid]">
<table class="table">
    <tbody>
    <tr>
        <td>销售*</td>
        <td>
            <?php
            $employee = new EmployeeBasicInformation();
            $uuids = $employee->getOrdinateUuids(RBACManager::CustomerModule);
            $records = $employee->getEmployeeListByUuids($uuids);
            $employeeList = $employee->transformForDropDownList($records['employeeList'], 'uuid', 'name');
            ?>
            <?= Html::dropDownList('AnniversaryAchievementForm[sales_uuid]',
                isset($formData['sales_uuid'])?$formData['sales_uuid']:'',
                ViewHelper::appendElementOnDropDownList($employeeList),[
                    'class'=>'form-control enableEdit sales-uuid',
                    'disabled'=>isset($show) && $show,
                    'data-parsley-required'=>true,
                ])?>
        </td>
        <td>年度</td>
        <td>
            <?= Html::dropDownList('AnniversaryAchievementForm[year]',
                isset($formData['year'])?$formData['year']:'',
                SalesAnniversaryAchievementStatistic::$yearList,[
                    'class'=>'form-control year',
                    'disabled'=>isset($show) && $show,
                    'data-parsley-type'=>"number",
                ])?>
        </td>
        <td>年度目标*</td>
        <td>
            <?= Html::textInput('AnniversaryAchievementForm[anniversary_target]',
                isset($formData['anniversary_target'])?$formData['anniversary_target']:'',
                [
                    'data-parsley-required'=>true,
                    'class'=>'form-control enableEdit',
                    'disabled'=>isset($show) && $show,
                    'data-parsley-type'=>"number",
                ])?>
        </td>
    </tr>
    <tr>
        <td>完成金额</td>
        <td>
            <?= Html::textInput('AnniversaryAchievementForm[achieved]',
                isset($formData['achieved'])?$formData['achieved']:'',
                [
                    'class'=>'form-control',
                    'disabled'=>true,
                ])?>
        </td>
        <td>已开票金额</td>
        <td>
            <?= Html::textInput('AnniversaryAchievementForm[checked_stamp_money]',
                isset($formData['checked_stamp_money'])?$formData['checked_stamp_money']:'',
                [
                    'class'=>'form-control',
                    'disabled'=>true,
                ])?>
        </td>
        <td>已收回金额</td>
        <td>
            <?= Html::textInput('AnniversaryAchievementForm[received_money]',
                isset($formData['received_money'])?$formData['received_money']:'',
                [
                    'class'=>'form-control',
                    'disabled'=>true,
                ])?>
        </td>
    </tr>
    <tr>
        <td>M1*</td>
        <td>
            <?= Html::textInput('AnniversaryAchievementForm[m1_target]',
                isset($formData['m1_target'])?$formData['m1_target']:'',
                [
                    'data-parsley-required'=>true,
                    'class'=>'form-control enableEdit',
                    'disabled'=>isset($show) && $show,
                    'data-parsley-type'=>"number",
                ])?>
        </td>
        <td>M2*</td>
        <td>
            <?= Html::textInput('AnniversaryAchievementForm[m2_target]',
                isset($formData['m2_target'])?$formData['m2_target']:'',
                [
                    'data-parsley-required'=>true,
                    'class'=>'form-control enableEdit',
                    'disabled'=>isset($show) && $show,
                    'data-parsley-type'=>"number",
                ])?>
        </td>
        <td>M3*</td>
        <td>
            <?= Html::textInput('AnniversaryAchievementForm[m3_target]',
                isset($formData['m3_target'])?$formData['m3_target']:'',
                [
                    'data-parsley-required'=>true,
                    'class'=>'form-control enableEdit',
                    'disabled'=>isset($show) && $show,
                    'data-parsley-type'=>"number",
                ])?>
        </td>
    </tr>
    <tr>
        <td>M4*</td>
        <td>
            <?= Html::textInput('AnniversaryAchievementForm[m4_target]',
                isset($formData['m4_target'])?$formData['m4_target']:'',
                [
                    'data-parsley-required'=>true,
                    'class'=>'form-control enableEdit',
                    'disabled'=>isset($show) && $show,
                    'data-parsley-type'=>"number",
                ])?>
        </td>
        <td>M5*</td>
        <td>
            <?= Html::textInput('AnniversaryAchievementForm[m5_target]',
                isset($formData['m5_target'])?$formData['m5_target']:'',
                [
                    'data-parsley-required'=>true,
                    'class'=>'form-control enableEdit',
                    'disabled'=>isset($show) && $show,
                    'data-parsley-type'=>"number",
                ])?>
        </td>
        <td>M6*</td>
        <td>
            <?= Html::textInput('AnniversaryAchievementForm[m6_target]',
                isset($formData['m6_target'])?$formData['m6_target']:'',
                [
                    'data-parsley-required'=>true,
                    'class'=>'form-control enableEdit',
                    'disabled'=>isset($show) && $show,
                    'data-parsley-type'=>"number",
                ])?>
        </td>
    </tr>
    <tr>
        <td>M7*</td>
        <td>
            <?= Html::textInput('AnniversaryAchievementForm[m7_target]',
                isset($formData['m7_target'])?$formData['m7_target']:'',
                [
                    'data-parsley-required'=>true,
                    'class'=>'form-control enableEdit',
                    'disabled'=>isset($show) && $show,
                    'data-parsley-type'=>"number",
                ])?>
        </td>
        <td>M8*</td>
        <td>
            <?= Html::textInput('AnniversaryAchievementForm[m8_target]',
                isset($formData['m8_target'])?$formData['m8_target']:'',
                [
                    'data-parsley-required'=>true,
                    'class'=>'form-control enableEdit',
                    'disabled'=>isset($show) && $show,
                    'data-parsley-type'=>"number",
                ])?>
        </td>
        <td>M9*</td>
        <td>
            <?= Html::textInput('AnniversaryAchievementForm[m9_target]',
                isset($formData['m9_target'])?$formData['m9_target']:'',
                [
                    'data-parsley-required'=>true,
                    'class'=>'form-control enableEdit',
                    'disabled'=>isset($show) && $show,
                    'data-parsley-type'=>"number",
                ])?>
        </td>
    </tr>
    <tr>
        <td>M10*</td>
        <td>
            <?= Html::textInput('AnniversaryAchievementForm[m10_target]',
                isset($formData['m10_target'])?$formData['m10_target']:'',
                [
                    'data-parsley-required'=>true,
                    'class'=>'form-control enableEdit',
                    'disabled'=>isset($show) && $show,
                    'data-parsley-type'=>"number",
                ])?>
        </td>
        <td>M11*</td>
        <td>
            <?= Html::textInput('AnniversaryAchievementForm[m11_target]',
                isset($formData['m11_target'])?$formData['m11_target']:'',
                [
                    'data-parsley-required'=>true,
                    'class'=>'form-control enableEdit',
                    'disabled'=>isset($show) && $show,
                    'data-parsley-type'=>"number",
                ])?>
        </td>
        <td>M12*</td>
        <td>
            <?= Html::textInput('AnniversaryAchievementForm[m12_target]',
                isset($formData['m12_target'])?$formData['m12_target']:'',
                [
                    'data-parsley-required'=>true,
                    'class'=>'form-control enableEdit',
                    'disabled'=>isset($show) && $show,
                    'data-parsley-type'=>"number",
                ])?>
        </td>
    </tr>
    <tr><td colspan="6"></td></tr>
    </tbody>
</table>
<span class="col-md-12">
    <span class="col-md-4"></span>
    <span class="col-md-4 displayBlockWhileEdit" style="display: <?= isset($show)&&$show?'none':'block'?>">
        <input url="<?= Url::to([
            '/statistic/sales-statistic/validate-anniversary-achievement'
        ])?>" type="<?= isset($validate) && $validate ? 'button' : 'submit'?>" value="提交"
       class="form-control btn-primary <?= isset($validate) && $validate ? 'validate-submit':''?>">
    </span>
    <span class="col-md-4"></span>
</span>
<?= Html::endForm()?>
<?= $this->render('@webroot/../views/site/error-modal',[
    'message'=>'亲，验证失败，请检查一下这个销售的该年度目标是否已经制定好！！'
])?>
</div>
<?php
$JS = <<<JS
$('.AnniversaryAchievementForm').on('click', '.validate-submit', function() {
    var self = $(this);
    var form = self.parents('form');
    var url = self.attr('url') + '&sales_uuid=' + 
    form.find('.sales-uuid').val() + '&year=' + form.find('.year').val();
    var container = self.parents('.form-container');
    $.get(url, function(data, status) {
        if(status !== 'success') {
            return ;
        }
        if(data != 1) {
            container.find('.error-modal').modal('show');
            return;
        }
        
        form.submit();
    })
})
JS;
$this->registerJs($JS, \yii\web\View::POS_END);
?>
