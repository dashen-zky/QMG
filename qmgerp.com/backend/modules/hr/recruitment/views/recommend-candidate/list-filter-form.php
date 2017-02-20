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
            <td>岗位</td>
            <td>
                <?= Html::dropDownList('ListFilterForm[position_uuid]',
                    isset($formData['position_uuid'])?$formData['position_uuid']:null,
                    ViewHelper::appendElementOnDropDownList($position->positionListDropDown()),
                    [
                        'class'=>'form-control',
                    ])?>
            </td>
            <td>创建人</td>
            <td>
                <?= Html::textInput('ListFilterForm[created_name]',
                    isset($formData['created_name'])?$formData['created_name']:'', [
                        'class' => 'form-control'
                    ])?>
            </td>
            <td>
                <?= Html::submitButton('搜索', ['class' => 'form-control btn btn-primary']) ?>
            </td>
        </tr>

        </tbody>
    </table>
<?= Html::endForm()?>