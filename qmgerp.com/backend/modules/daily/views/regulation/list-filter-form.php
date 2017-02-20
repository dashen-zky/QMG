<?php
use yii\helpers\Html;
use backend\models\ViewHelper;
use backend\modules\daily\models\regulation\RegulationConfig;
$config = new RegulationConfig();
?>

<?= Html::beginForm(['/daily/regulation/list-filter'], 'post', ['data-pjax' => '', 'class' => 'ListFilterForm']); ?>
    <table class="table">
        <tbody>
        <tr>
            <td class="col-md-1">编号</td>
            <td class="col-md-3">
                <?= Html::input('text', 'ListFilterForm[code]',
                    isset($formData['code'])?$formData['code']:'',
                    [
                        'class' => 'form-control'
                    ]) ?>
            </td>
            <td class="col-md-1">标题</td>
            <td class="col-md-3">
                <?= Html::input('text', 'ListFilterForm[title]',
                    isset($formData['title'])?$formData['title']:'',
                    [
                        'class' => 'form-control'
                    ]) ?>
            </td>
            <td class="col-md-1">标签</td>
            <td class="col-md-3">
                <?= Html::input('text', 'ListFilterForm[tags]',
                    isset($formData['tags'])?$formData['tags']:'',
                    [
                        'class' => 'form-control'
                    ]) ?>
            </td>
        </tr>
        <tr>
            <td>类型</td>
            <td>
                <?= Html::dropDownList('ListFilterForm[type]',
                    isset($formData['type'])?$formData['type']:'',
                    ViewHelper::appendElementOnDropDownList($config->getList('type')),[
                        'class' => 'form-control'
                    ])?>
            </td>
            <td>跟新日期</td>
            <td>
                <span class="col-md-6" style="padding: 0px">
                <?= Html::textInput('ListFilterForm[min_update_time]',
                    isset($formData['min_update_time'])?$formData['min_update_time']:'',
                    [
                        'class' => 'input-section datetimepicker form-control col-md-12',
                        'placeholder'=>'开始日期'
                    ]) ?>
                </span>
                <span class="col-md-6" style="padding: 0px">
                    <?= Html::textInput('ListFilterForm[max_update_time]',
                        isset($formData['max_update_time'])?$formData['max_update_time']:'',
                        [
                            'class' => 'input-section datetimepicker form-control col-md-12',
                            'placeholder'=>'截止日期'
                        ]) ?>
                </span>
            </td>
            <td>
                <?= Html::submitButton('搜索', ['class' => 'form-control btn btn-primary submit']) ?>
            </td>
            <td></td>
        </tr>

        </tbody>
    </table>
<?= Html::endForm()?>