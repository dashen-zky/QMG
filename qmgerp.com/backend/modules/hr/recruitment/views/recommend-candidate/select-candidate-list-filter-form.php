<?php
use yii\helpers\Html;
use backend\models\ViewHelper;
use backend\modules\hr\models\Position;
$position = new Position();
$config = new \backend\modules\hr\recruitment\models\ApplyRecruitConfig();
?>

<?= Html::beginForm($action, 'post', ['data-pjax' => '', 'class' => 'ListFilterForm']); ?>
<input hidden name="ListFilterForm[recruit_uuid]" value="<?= $recruit_uuid?>">
<table class="table">
    <tbody>
    <tr>
        <td class="col-md-1">姓名</td>
        <td class="col-md-3">
            <?= Html::textInput('ListFilterForm[name]',
                isset($formData['name'])?$formData['name']:'', [
                    'class' => 'form-control'
                ])?>
        <td class="col-md-1">电话</td>
        <td class="col-md-3">
            <?= Html::textInput('ListFilterForm[phone]',
                isset($formData['phone'])?$formData['phone']:'', [
                    'class' => 'form-control'
                ])?>
        </td>
        <td class="col-md-1">邮箱</td>
        <td class="col-md-3">
            <?= Html::textInput('ListFilterForm[email]',
                isset($formData['email'])?$formData['email']:'', [
                    'class' => 'form-control'
                ])?>
        </td>
    </tr>
    <tr>
        <td>职位</td>
        <td>
            <?= Html::textInput('ListFilterForm[position]',
                isset($formData['position'])?$formData['position']:'', [
                    'class' => 'form-control'
                ])?>
        </td>
        <td>
            <?= Html::button('搜索', [
                'class' => 'form-control btn btn-primary submit',
            ]) ?>
        </td>
        <td colspan="3"></td>
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
            }
        });
    });
})
JS;
$this->registerJs($JS, \yii\web\View::POS_END);
?>

