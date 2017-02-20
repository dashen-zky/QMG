<?php
use yii\helpers\Json;
use backend\modules\statistic\models\ProjectAnniversaryAchievement;
use backend\modules\statistic\models\ProjectStatistic;
$projectStatistic = new ProjectStatistic();
$projectAchievementStatic = new ProjectAnniversaryAchievement();
?>
<input hidden class="menu" value='<?= Json::encode([
    'menu-1-project',
    'menu-2-project-statistic'
])?>'>
<div class="col-md-12" style="height: 100%">
    <ul class="nav nav-tabs">
        <li class="<?= !isset($tab) || $tab == 'project'?'active':''?>"><a href="#project-statistic" data-toggle="tab">项目统计</a></li>
        <li class="<?= isset($tab) && $tab == 'achievement'?'active':''?>"><a href="#achievement-statistic" data-toggle="tab">业绩统计</a></li>
        <li class="<?= isset($tab) && $tab == 'add-target'?'active':''?>"><a href="#add-target" data-toggle="tab">添加目标</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade <?= !isset($tab) || $tab == 'project'?'active in':''?>" id="project-statistic">
            <?= $this->render('project-statistic-list', [
                'statisticList'=>isset($projectStatisticList)?$projectStatisticList : $projectStatistic->myStatisticList(),
                'ser_filter'=>isset($project_statistic_ser_filter)?$project_statistic_ser_filter:'',
            ])?>
        </div>
        <div class="tab-pane fade <?= isset($tab) && $tab == 'achievement'?'active in':''?>" id="achievement-statistic">
            <?= $this->render('anniversary-achievement-list',[
                'ser_filter'=>isset($anniversary_achievement_ser_filter)?$anniversary_achievement_ser_filter:'',
                'anniversaryAchievementList'=>isset($anniversaryAchievementList)?$anniversaryAchievementList:$projectAchievementStatic->myAnniversaryAchievementList(),
            ])?>
        </div>
        <div class="tab-pane fade <?= isset($tab) && $tab == 'add-target'?'active in':''?>" id="add-target">
            <?= $this->render('anniversary-achievement-form',[
                'action'=>['/statistic/project-statistic/add-anniversary-achievement'],
                'validate'=>true,
            ])?>
        </div>
    </div>
</div>