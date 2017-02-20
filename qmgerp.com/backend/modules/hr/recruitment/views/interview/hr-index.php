<?php
use yii\helpers\Json;
use backend\modules\hr\recruitment\models\RecruitCandidateMap;
$candidate = new RecruitCandidateMap();
?>
<input hidden class="menu" value='<?= Json::encode([
    'menu-1-hr',
    'menu-2-interview',
])?>'>
<div class="col-md-12" style="height: 100%">
    <ul class="nav nav-tabs">
        <li class="<?= (!isset($tab) || $tab === 'interview-list')?'active':''?>"><a href="#interview-list" data-toggle="tab">候选人</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade <?= (!isset($tab) || $tab === 'interview-list')?'active in':''?>" id="interview-list">
            <?= $this->render('list-panel',[
                'candidateList'=>isset($candidateList)?$candidateList:$candidate->interviewList(),
                'ser_filter'=>isset($ser_filter)?$ser_filter:'',
                'list_filter_action'=>['/recruitment/interview/list-filter'],
                'list_file'=>'hr-list'
            ])?>
        </div>
    </div>
</div>
