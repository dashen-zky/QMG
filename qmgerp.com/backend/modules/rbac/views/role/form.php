<?php
use backend\modules\hr\models\EmployeeForm;
use backend\models\ViewHelper;
use yii\helpers\Html;
use yii\web\View;
use backend\modules\rbac\model\RoleManager;
?>
<?= Html::beginForm($action, 'post', [
    'class' => 'RoleForm',
    'data-parsley-validate' => "true",
]); ?>
<?php if($show):?>
    <div style="float: right">
        <a href="javascript:;" class="editForm" style="font-size: 15px"><i class="fa fa-2x fa-pencil"></i>编辑</a>
    </div>
<?php endif?>
    <table class="table">
        <tbody>
        <tr>
            <td class="col-md-2">*角色id</td>
            <td class="col-md-4">
                <div class="col-md-12">
                    <?= Html::textInput('RoleForm[name]',
                        isset($formData['name'])?$formData['name']:'',
                        [
                            'readOnly'=>$show,
                            'class' => 'form-control col-md-12',
                            'data-parsley-required'=>'true',
                        ]) ?>
                </div>
            </td>
            <td class="col-md-2">父角色</td>
            <td class="col-md-4">
                <div class="col-md-12">
                    <?php $roleList = RoleManager::buildRoleListForDropDownList();?>
                    <?= Html::dropDownList(
                        'RoleForm[parent]',
                        null,
                        ViewHelper::appendElementOnDropDownList($roleList),
                        [
                            'class'=>'enableEdit form-control department col-md-12',
                            'disabled'=>$show,
                            'options'=>ViewHelper::defaultValueForDropDownList(true, $formData,'parent'),
                        ]
                    ) ?>
                </div>
            </td>
        </tr>
        <tr>
            <td class="col-md-2">角色描述</td>
            <td colspan="3">
                <div class="col-md-12">
                    <?= Html::textarea('RoleForm[description]',
                        isset($formData['description'])?$formData['description']:'',
                        [
                            'class' => 'enableEdit form-control col-md-12',
                            'disabled'=>$show,
                            'rows'=>3,
                        ]) ?>
                </div>
            </td>
        </tr>
        <tr>
        </tr>
        </tbody>
    </table>
    <span class="col-md-12">
    <span class="col-md-4"></span>
    <span class="col-md-4">
        <input type="reset" name="reset" style="display: none;" />
        <input type="submit" <?= $show?'disabled':'' ?> value="提交" class="enableEdit form-control btn-primary">
    </span>
    <span class="col-md-4"></span>
    </span>
<?= Html::endForm()?>