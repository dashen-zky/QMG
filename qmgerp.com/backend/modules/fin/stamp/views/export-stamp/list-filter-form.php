<?php
use backend\models\ViewHelper;
use yii\helpers\Html;
use backend\modules\fin\stamp\models\StampConfig;
$stampConfig = new StampConfig();
?>

<?= Html::beginForm($action, 'post', ['data-pjax' => '', 'class' => 'ListFilterForm']); ?>
    <table class="table">
        <tbody>
        <tr>
            <td class="col-md-1">发票编号</td>
            <td class="col-md-3">
                <div class="col-md-12">
                    <?= Html::textInput('ListFilterForm[series_number]',
                        isset($formData['series_number'])?$formData['series_number']:'',
                        [
                            'class' => 'form-control col-md-12',
                        ]) ?>
                </div>
            </td>
            <td class="col-md-1">发票类型</td>
            <td class="col-md-3">
                <div class="col-md-12">
                    <?= Html::dropDownList(
                        'ListFilterForm[service_type]',
                        isset($formData['service_type'])?$formData['service_type']:null,
                        ViewHelper::appendElementOnDropDownList($stampConfig->getList('service_type')),
                        [
                            'class'=>'form-control col-md-12',
                        ]
                    ) ?>
                </div>
            </td>
            <td class="col-md-1">状态</td>
            <td class="col-md-3">
                <div class="col-md-12">
                <?= Html::dropDownList(
                    'ListFilterForm[status]',
                    isset($formData['status'])?$formData['status']:null,
                    ViewHelper::appendElementOnDropDownList($stampConfig->getList('import_status')),
                    [
                        'class'=>'form-control department col-md-12',
                    ]
                ) ?>
                </div>
            </td>
        </tr>
        <tr>
            <td>金额</td>
            <td>
                <div class="col-md-12">
                    <span class="col-md-6" style="padding: 0px">
                    <?= Html::textInput('ListFilterForm[min_money]',
                        isset($formData['min_money'])?$formData['min_money']:'',
                        [
                            'class' => 'form-control col-md-12',
                            'placeholder'=>'最小金额'
                        ]) ?>
                </span>
                    <span class="col-md-6" style="padding: 0px">
                    <?= Html::textInput('ListFilterForm[max_money]',
                        isset($formData['max_money'])?$formData['max_money']:'',
                        [
                            'class' => 'form-control col-md-12',
                            'placeholder'=>'最大金额'
                        ]) ?>
            </span>
                </div>
            </td>
            <td>开票日期</td>
            <td>
                <div class="col-md-12">
                    <span class="col-md-6" style="padding: 0px">
                    <?= Html::textInput('ListFilterForm[min_made_time]',
                        isset($formData['min_made_time'])?$formData['min_made_time']:'',
                        [
                            'class' => 'input-section datetimepicker form-control col-md-12',
                            'placeholder'=>'开始日期'
                        ]) ?>
                    </span>
                    <span class="col-md-6" style="padding: 0px">
                        <?= Html::textInput('ListFilterForm[max_made_time]',
                            isset($formData['max_made_time'])?$formData['max_made_time']:'',
                            [
                                'class' => 'input-section datetimepicker form-control col-md-12',
                                'placeholder'=>'截止日期'
                            ]) ?>
                    </span>
                </div>
            </td>
            <td>开票方</td>
            <td>
                <div class="col-md-12">
                    <?= Html::textInput('ListFilterForm[provider]',
                        isset($formData['provider'])?$formData['provider']:'',
                        [
                            'class' => 'form-control col-md-12',
                        ]) ?>
                </div>
            </td>
        </tr>
        <tr>
            <td>收票方</td>
            <td>
                <div class="col-md-12">
                    <?= Html::textInput('ListFilterForm[receiver]',
                        isset($formData['receiver'])?$formData['receiver']:'',
                        [
                            'class' => 'form-control col-md-12',
                        ]) ?>
                </div>
            </td>
            <td>
            </td>
            <td colspan="4"></td>
        </tr>
        <tr>
            <td><?= Html::button('搜索', [
                    'class' => 'form-control btn btn-primary submit',
                ]) ?></td>
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
                var panel = form.parent('.panel-body');
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
