<?php
use yii\widgets\LinkPager;
use backend\models\ListIndex;
?>
<!-- begin panel -->
<div class="panel-heading">
    <div class="panel-heading-btn">
        <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
        <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-repeat"></i></a>
        <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
        <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></a>
    </div>
    <h4 class="panel-title">部门列表</h4>
</div>

<div class="panel-body">
    <table class="table table-striped">
        <thead>
        <tr>
            <th>选择</th>
            <th>#</th>
            <th>名称</th>
            <th class="col-md-4">部门描述</th>
            <th>上级部门</th>
        </tr>
        </thead>
        <tbody>
        <?php $i = 1;?>
        <?php foreach ($department['departmentList'] as $item) :?>
            <tr>
                <td><input type="radio" name="uuid" value="<?= $item['uuid']?>"></td>
                <td><?= ListIndex::listIndex($i)?></td>
                <td class="departmentName"><?= $item['name']?></td>
                <td><?= $item['description']?></td>
                <td><?= isset($item['parent_name'])?$item['parent_name']:null?></td>
            </tr>
            <?php $i++;?>
        <?php endforeach?>
        </tbody>
    </table>
    <?= LinkPager::widget(['pagination' => $department['pagination']]); ?>
</div>
<!-- end panel -->