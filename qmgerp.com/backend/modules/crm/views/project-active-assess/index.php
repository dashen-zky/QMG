<?php
use yii\helpers\Json;
use backend\modules\crm\models\project\record\Project;
$project = new Project();
?>
<input hidden class="menu" value='<?= Json::encode([
    'menu-1-assess',
    'menu-2-project-active-assess',
])?>'>
<div class="col-md-12" style="height: 100%">
    <ul class="nav nav-tabs">
        <li class='active'><a href="#list" data-toggle="tab">项目列表</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade active in" id="list">
            <?= $this->render('list', [
                'projectList'=>isset($projectList)?$projectList:$project->activeAssessList(),
                'ser_filter'=>isset($ser_filter)?$ser_filter:'',
            ])?>
        </div>
    </div>
</div>
