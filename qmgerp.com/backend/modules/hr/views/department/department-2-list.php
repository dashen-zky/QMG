<?php
use yii\widgets\Pjax;
use yii\widgets\LinkPager;
use yii\helpers\Url;
use yii\web\View;
?>
<!-- begin panel -->
<div class="panel panel-body">
    <?php Pjax::begin(); ?>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>#</th>
            <th>名称</th>
            <th class="col-md-4">部门描述</th>
            <th>负责人</th>
            <th>所属公司</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($department['departmentList'] as $item) :?>
            <tr>
                <td><?= $item['code']?></td>
                <td><?= $item['name']?></td>
                <td><?= $item['description']?></td>
                <td><?= $item['duty_name']?></td>
                <td><?= $item['parent_name']?></td>
                <td>
                    <div><a href="<?= Url::to([
                            '/hr/department/edit',
                            'uuid'=>$item['uuid'],
                        ])?>" id="<?= $item['uuid']?>">编辑</a></div>
                </td>
            </tr>
        <?php endforeach?>
        </tbody>
    </table>
    <?= LinkPager::widget(['pagination' => $department['pagination']]); ?>
    <?php Pjax::end(); ?>
</div>
<!-- end panel -->