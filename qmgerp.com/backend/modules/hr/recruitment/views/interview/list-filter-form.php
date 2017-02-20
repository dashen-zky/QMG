<?php
use yii\helpers\Html;
use backend\models\ViewHelper;
use backend\modules\hr\models\Position;
$position = new Position();
$config = new \backend\modules\hr\recruitment\models\CandidateConfig();
?>

<?= Html::beginForm($action, 'post', ['data-pjax' => '', 'class' => 'ListFilterForm']); ?>
<table class="table">
    <tbody>
    <tr>
        <td class="col-md-1">姓名</td>
        <td class="col-md-3">
            <?= Html::textInput('ListFilterForm[name]',
                isset($formData['name'])?$formData['name']:'', [
                    'class' => 'form-control'
                ])?>
        </td>
        <td class="col-md-1">候选人</td>
        <td class="col-md-3">
            <?= Html::textInput('ListFilterForm[interview_name]',
                isset($formData['interview_name'])?$formData['interview_name']:'', [
                    'class' => 'form-control'
                ])?>
        </td>
        <td class="col-md-1">电话</td>
        <td class="col-md-3">
            <?= Html::textInput('ListFilterForm[phone]',
                isset($formData['phone'])?$formData['phone']:'', [
                    'class' => 'form-control'
                ])?>
        </td>
    </tr>
    <tr>
        <td class="col-md-1">邮箱</td>
        <td class="col-md-3">
            <?= Html::textInput('ListFilterForm[email]',
                isset($formData['email'])?$formData['email']:'', [
                    'class' => 'form-control'
                ])?>
        </td>
        <td>岗位</td>
        <td>
            <?= Html::dropDownList('ListFilterForm[position_uuid]',
                isset($formData['position_uuid'])?$formData['position_uuid']:null,
                ViewHelper::appendElementOnDropDownList($position->canRecruitPositionList()),
                [
                    'class'=>'form-control',
                ])?>
        </td>
        <td>状态</td>
        <td>
            <?= Html::dropDownList('ListFilterForm[status]',
                isset($formData['status'])?$formData['status']:null,
                ViewHelper::appendElementOnDropDownList($config->getList('status')),
                [
                    'class'=>'form-control',
                ])?>
        </td>
    </tr>
    <tr>
        <td>
            <?= Html::button('搜索', [
                'class' => 'form-control btn btn-primary submit',
            ]) ?>
        </td>
        <td colspan="5"></td>
    </tr>

    </tbody>
</table>
<?= Html::endForm()?>
<?php
$JS = <<<JS
$(function() {
    $('.ListFilterForm').on('click', '.submit', function() {
        var form = $(this).parents('form');
        var url = form.attr('action');
        $.ajax({
            url: url,
            type: 'post',
            data: form.serialize(),
            success: function (data) {
                var panel = form.parent('.panel');
                var container = panel.find('.list');
                container.html(data);
                // 检查一下有没有选择发票
                
                panel.find('.pagination').on('click', 'li', function() {
                    pagination($(this));
                });
                
                panel.on('click','.select-all',function() {
                    var container = $(this).parents('.candidate-list-container');
                    $.each(container.find('table .candidate-uuid'), function() {
                        var checked = $(this).attr('checked');
                        if(checked != 'checked') {
                            $(this).attr('checked', 'checked');
                        }
                    });
                });
            }
        });
    });
})
JS;
$this->registerJs($JS, \yii\web\View::POS_END);
?>

