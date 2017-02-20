<?php
use backend\models\MyLinkPage;
use backend\modules\crm\models\project\model\ProjectConfig;
$config = new ProjectConfig();
?>

<?= $this->render('list-filter-form',[
    'action'=>isset($entrance) && $entrance == 'project-apply-payment'
        ? ['/crm/project-apply-payment/project-list-filter'] :
         ['/crm/supplier-apply-payment/project-list-filter'],
    'formData'=>unserialize(isset($ser_filter)?$ser_filter:''),
]);?>
<table class="table">
    <thead>
    <th>选择</th>
    <th>编码</th>
    <th>项目名称</th>
    <th>项目状态</th>
    <th>客户名称</th>
    <th>项目经理</th>
    <th>销售经理</th>
    </thead>
    <tbody>
    <?php foreach($list['projectList'] as $item) :?>
    <tr>
        <td>
            <input type="radio" <?= in_array($item['status'], [
                ProjectConfig::StatusExecuting,
                ProjectConfig::StatusDone,
                ProjectConfig::StatusDoneApplying
            ])?'':'disabled'?> name="uuid" value="<?= $item['uuid']?>">
        </td>
        <td><?= \backend\modules\crm\models\project\model\ProjectForm::codePrefix . $item['code']?></td>
        <td class="name"><?= $item['name']?></td>
        <td><?= $config->getAppointed('projectStatus', $item['status'])?></td>
        <td><?= $item['customer_name']?></td>
        <td><?= $item['project_manager_name']?></td>
        <td><?= $item['sales_name']?></td>
    </tr>
    <?php endforeach?>
    </tbody>
</table>

<?php
if(isset($ser_filter) && !empty($ser_filter)) {
    $pageParams = [
        'pagination' => $list['pagination'],
        'ser_filter'=>$ser_filter,
    ];
} else {
    $pageParams = [
        'pagination' => $list['pagination'],
    ];
}
?>
<?= MyLinkPage::widget($pageParams); ?>
