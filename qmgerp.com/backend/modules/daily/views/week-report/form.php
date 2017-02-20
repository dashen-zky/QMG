<?php
use yii\helpers\Html;
use yii\helpers\Json;
use backend\modules\hr\models\Department;
use yii\helpers\Url;
?>
<?= Html::beginForm($action, 'post', [
    'class' => 'form-horizontal WeekReportForm',
    'data-parsley-validate' => "true",
])?>
<input value="<?= isset($formData['uuid'])?$formData['uuid'] : ''?>" hidden name="WeekReportForm[uuid]">
<table class="table">
    <tr>
        <td class="col-md-1">标题*</td>
        <td class="col-md-3">
            <?= Html::textInput('WeekReportForm[title]',
                isset($formData['title'])?$formData['title']:null,
                [
                    'disabled'=>isset($show)&&$show,
                    'class'=>'form-control enableEdit',
                    'data-parsley-required'=>'true',
                ])?>
        </td>
        <td>创建人</td>
        <td>
            <?= Html::textInput('WeekReportForm[created_name]',
                isset($formData['created_name'])?$formData['created_name']:
                    Yii::$app->user->getIdentity()->getEmployeeName(),
                [
                    'disabled'=>true,
                    'class'=>'form-control',
                ])?>
        </td>
        <td class="col-md-1">创建日期</td>
        <td class="col-md-3">
            <?= Html::textInput("WeekReportForm[created_time]",
                isset($formData['created_time']) &&
                $formData['created_time'] != 0 ?date("Y-m-d",$formData['created_time']):null,[
                    'class'=>'form-control',
                    'disabled'=>true,
                ])?>
        </td>
    </tr>
    <tr>
    <tr>
        <td>本周内容*</td>
        <td colspan="5">
            <?= Html::textarea('WeekReportForm[content]',
                isset($formData['content'])?$formData['content']:null,
                [
                    'disabled'=>isset($show)&&$show,
                    'class'=>'form-control enableEdit',
                    'data-parsley-required'=>'true',
                    'rows'=>'5',
                ])?>
        </td>
    </tr>
    <tr>
        <td>下周安排*</td>
        <td colspan="5">
            <?= Html::textarea('WeekReportForm[next_content]',
                isset($formData['next_content'])?$formData['next_content']:null,
                [
                    'disabled'=>isset($show)&&$show,
                    'class'=>'form-control enableEdit',
                    'data-parsley-required'=>'true',
                    'rows'=>'5',
                ])?>
        </td>
    </tr>
    <tr>
        <td>事项</td>
        <td colspan="5">
            <span class="selected-transaction-tags" style="margin-bottom: 10px; float: left">
                <ul class="float-left">
                </ul>
            </span>
            <input class="transaction-uuid" hidden name="WeekReportForm[transaction_uuid]">
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
$(function () {
})
JS;

$this->registerJs($JS, \yii\web\View::POS_END);
?>
