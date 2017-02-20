<?php
use yii\widgets\Pjax;
use backend\models\MyLinkPage;
use backend\modules\crm\models\project\model\ProjectConfig;
use yii\helpers\Url;
$config = new ProjectConfig();
?>
<!-- begin panel -->
<div class="panel project-list-panel">
<?php Pjax::begin(); ?>
    <?= $this->render('list-filter-form',[
        'formData'=>unserialize(isset($ser_filter)?$ser_filter:''),
    ]);?>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>编号</th>
            <th class="col-md-1">客户简称</th>
            <th>项目名称</th>
            <th>项目状态</th>
            <th>项目金额</th>
            <th>销售</th>
            <th>项目经理</th>
            <th>立项人</th>
            <th>立项日期</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($projectList['projectList'] as $item) :?>
            <tr>
                <td>
                    <?= \backend\modules\crm\models\project\model\ProjectForm::codePrefix.$item['code']?>
                </td>
                <td><?= $item['customer_name']?></td>
                <td>
                    <a url="<?= Yii::$app->urlManager->createUrl([
                        '/crm/project/edit',
                        'uuid'=>$item['uuid']
                    ])?>" href="#" class="show-project">
                        <?= $item['name']?>
                    </a>
                </td>
                <td><?= $config->getAppointed('projectStatus',$item['status'])?></td>
                <td><?= $item['actual_money_amount']?></td>
                <td><?= $item['sales_name']?></td>
                <td><?= $item['project_manager_name']?></td>
                <td><?= $item['apply_active_name']?></td>
                <td><?= $item['active_time'] != 0 ? date('Y-m-d', $item['active_time']):null ?></td>
                <td>
                    <?php if($item['status'] == ProjectConfig::StatusExecuteApplying) :?>
                    <a href="<?= Url::to([
                        '/crm/project/active-assess-passed',
                        'uuid'=>$item['uuid']
                    ])?>">通过</a>
                    <a href="#" class="assess-refused" uuid="<?= $item['uuid']?>">不通过</a>
                    <?php endif;?>
                </td>
            </tr>
        <?php endforeach?>
        </tbody>
</table>
    <?php
    if(isset($ser_filter) && !empty($ser_filter)) {
        $pageParams = [
            'pagination' => $projectList['pagination'],
            'ser_filter'=>$ser_filter,
        ];
    } else {
        $pageParams = [
            'pagination' => $projectList['pagination'],
        ];
    }
    ?>
    <?= MyLinkPage::widget($pageParams); ?>
<?php Pjax::end(); ?>
<?= $this->render('refuse-reason')?>
</div>
<!-- end panel -->
<?php
$JS = <<<JS
$('.project-list-panel').on('click','.show-project',function() {
        var url = $(this).attr('url');
        window.open(url); 
    });

$('.project-list-panel').on('click','.assess-refused', function() {
    var panel = $(this).parents('.project-list-panel');
    var modal = panel.find('.refuse-reason-modal');
    modal.find('.uuid').val($(this).attr('uuid'));
    modal.modal('show');
});
JS;
$this->registerJs($JS, \yii\web\View::POS_END);
?>