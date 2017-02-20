<?php
use yii\widgets\Pjax;
use backend\models\MyLinkPage;
use yii\helpers\Url;
?>
<!-- begin panel -->
<div class="panel panel-body customer-statistic-list">
    <?php Pjax::begin(); ?>
    <?= $this->render('project-statistic-list-filter-form',[
        'formData'=>isset($ser_filter)?unserialize($ser_filter):'',
    ])?>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>姓名</th>
            <th class="col-md-2">部门</th>
            <th class="col-md-2">岗位</th>
            <th>项目总数</th>
            <th>跟进中项目数</th>
            <th>执行中项目数</th>
            <th>已结案项目数</th>
            <th>最近7天跟进数</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($statisticList['list'] as $item) :?>
            <tr>
                <td><a url="<?= Url::to([
                        '/crm/project/index',
                        'ser_filter'=>serialize(['project_manager_name'=>$item['manager_name']])
                    ])?>" href="#" class="show-new-tab"><?= $item['manager_name']?></a></td>
                <td><?= $item['department_name']?></td>
                <td><?= $item['position_name']?></td>
                <td><?= $item['projects_total']?></td>
                <td><?= $item['number_of_touching']?></td>
                <td><?= $item['number_of_executing']?></td>
                <td><?= $item['number_of_done']?></td>
                <td><?= $item['number_of_last_7_days_touch_records']?></td>
            </tr>
        <?php endforeach?>
        </tbody>
    </table>
    <?php
    if(isset($ser_filter) && !empty($ser_filter)) {
        $pageParams = [
            'pagination' => $statisticList['pagination'],
            'ser_filter'=>$ser_filter,
        ];
    } else {
        $pageParams = [
            'pagination' => $statisticList['pagination'],
        ];
    }
    ?>
    <?= MyLinkPage::widget($pageParams); ?>
    <?php Pjax::end(); ?>
</div>
<!-- end panel -->