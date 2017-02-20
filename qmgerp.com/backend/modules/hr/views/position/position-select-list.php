<?php
use yii\widgets\LinkPager;
use backend\models\ViewHelper;
use yii\helpers\Html;
use yii\helpers\Url;
?>
<!-- begin panel -->
<div class="panel-body">
    <table class="table table-striped">
        <thead>
        <tr>
            <th>选择</th>
            <th>#</th>
            <th>名称</th>
            <th>薪资范围</th>
            <th class="col-md-4">职位职责</th>
            <th>所在部门</th>
            <th>上级部门</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($position['positionList'] as $item) :?>
            <tr>
                <td><input type="checkbox"
                        <?= isset($checked) && in_array($item['uuid'], $checked)?'checked':''?>
                           class="position-uuid"
                           name="uuid"
                           id="<?= $item['uuid']?>"
                           value="<?= $item['uuid']?>">
                </td>
                <td><?= $item['code']?></td>
                <td class="position-name"><?= $item['name']?></td>
                <td><?= $item['min_salary']?>-<?= $item['max_salary']?></td>
                <td><?= $item['duty']?></td>
                <td><?= $item['departmentName']?></td>
                <td><?= $item['parentDepartmentName']?></td>
            </tr>
        <?php endforeach?>
        </tbody>
    </table>
</div>
<!-- end panel -->