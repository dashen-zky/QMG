<?php
use yii\helpers\Html;
use backend\models\ViewHelper;
use yii\helpers\Url;
?>
<?= Html::beginForm(['/hr/position/list-filter'], 'post', ['data-pjax' => '', 'class' => 'list-filter']); ?>
    <table class="table list-filter-table">
        <tbody>
        <tr>
            <td class="col-md-1">公司</td>
            <td class="col-md-3">
                <?= Html::dropDownList(
                    'ListFilterForm[department][1]',
                    null,
                    ViewHelper::appendElementOnDropDownList(isset($filters['department'][1])?
                        $filters['department'][1]:[]),
                    [
                        'data-parsley-required'=>'true',
                        'class'=>'form-control department-1 col-md-12',
                        'id'=>Url::to([
                            '/hr/position/department-list'
                        ]),
                        'options'=>ViewHelper::defaultValueForDropDownList(true, $filters,'department_level_1'),
                    ]
                ) ?>
            </td>
            <td class="col-md-1">事业部</td>
            <td class="col-md-3">
                <?= Html::dropDownList(
                    'ListFilterForm[department][2]',
                    null,
                    ViewHelper::appendElementOnDropDownList(isset($filters['department'][2])?
                        $filters['department'][2]:[]),
                    [
                        'class'=>'form-control department-2 col-md-12',
                        'id'=>Url::to([
                            '/hr/position/department-list'
                        ]),
                        'options'=>ViewHelper::defaultValueForDropDownList(true, $filters,'department_level_2'),
                    ]
                ) ?>
            </td>
            <td class="col-md-1">部门</td>
            <td class="col-md-3">
                <?= Html::dropDownList(
                    'ListFilterForm[department][3]',
                    null,
                    ViewHelper::appendElementOnDropDownList(isset($filters['department'][3])?
                        $filters['department'][3]:[]),
                    [
                        'id'=>Url::to([
                            '/hr/position/department-list'
                        ]),
                        'class'=>'form-control department-3 col-md-12',
                        'options'=>ViewHelper::defaultValueForDropDownList(true, $filters,'department_level_3'),
                    ]
                ) ?>
            </td>
            <td>
                <?= Html::submitButton('搜索', ['class' => 'form-control btn btn-primary submit']) ?>
            </td>
        </tr>
        </tbody>
    </table>
<?= Html::endForm()?>