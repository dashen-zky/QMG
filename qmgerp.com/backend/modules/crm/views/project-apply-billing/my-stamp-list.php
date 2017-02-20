<?php
use backend\models\MyLinkPage;
use backend\modules\crm\models\project\record\ProjectApplyStamp;
use yii\widgets\Pjax;
?>
<?php Pjax::begin()?>
<?= $this->render('list-filter-form',[
    'formData'=>unserialize(isset($ser_filter)?$ser_filter:''),
]);?>
<table class="table table-striped">
    <thead>
    <tr>
        <th>#</th>
        <th>项目</th>
        <th>项目编号</th>
        <th>申请金额</th>
        <th>已开票金额</th>
        <th>申请人</th>
        <th>申请时间</th>
        <th>状态</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($projectApplyStampList['list'] as $item) :?>
        <tr>
            <td><?= $item['id']?></td>
            <td>
                <?= $item['project_name']?>
            </td>
            <td>
                <?= \backend\modules\crm\models\project\model\ProjectForm::codePrefix.$item['project_code']?>
            </td>
            <td><?= $item['money']?></td>
            <td><?= $item['checked_stamp_money']?></td>
            <td><?= $item['created_name']?></td>
            <td><?= $item['created_time'] !='0'?date("Y-m-d", $item['created_time']):''?></td>
            <td><?= ProjectApplyStamp::$status[$item['status']]?></td>
        </tr>
    <?php endforeach?>
    </tbody>
</table>
<?php
if(isset($ser_filter) && !empty($ser_filter)) {
    $pageParams = [
        'pagination' => $projectApplyStampList['pagination'],
        'ser_filter'=>$ser_filter,
    ];
} else {
    $pageParams = [
        'pagination' => $projectApplyStampList['pagination'],
    ];
}
?>
<?= MyLinkPage::widget($pageParams); ?>
<?php Pjax::end()?>