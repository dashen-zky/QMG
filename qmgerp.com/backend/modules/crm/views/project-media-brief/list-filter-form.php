<?php
use yii\helpers\Html;
use backend\models\ViewHelper;
use backend\modules\hr\models\Position;
$position = new Position();
$config = new \backend\modules\hr\recruitment\models\ApplyRecruitConfig();
?>

<?= Html::beginForm($action, 'post', ['data-pjax' => '', 'class' => 'ListFilterForm']); ?>
<table class="table">
    <tbody>
    <tr>
        <td>标题</td>
        <td>
            <?= Html::textInput('ListFilterForm[title]',
                isset($formData['title'])?$formData['title']:'', [
                    'class' => 'form-control'
                ])?>
        </td>
        <td>创建人</td>
        <td>
            <?= Html::textInput('ListFilterForm[created_name]',
                isset($formData['created_name'])?$formData['created_name']:'', [
                    'class' => 'form-control'
                ])?>
        </td>
        <td>项目</td>
        <td>
            <?= Html::textInput('ListFilterForm[project_name]',
                isset($formData['project_name'])?$formData['project_name']:'', [
                    'class' => 'form-control'
                ])?>
    </tr>
    <tr>
        <td>客户</td>
        <td>
            <?= Html::textInput('ListFilterForm[customer_name]',
                isset($formData['customer_name'])?$formData['customer_name']:'', [
                    'class' => 'form-control'
                ])?>
        <td>
            <?= Html::button('搜索', ['class' => 'submit form-control btn btn-primary']) ?>
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
                var panel = form.parents('.panel');
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
