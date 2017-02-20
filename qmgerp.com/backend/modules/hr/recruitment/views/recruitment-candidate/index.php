<?php
use yii\helpers\Json;
use backend\modules\hr\recruitment\models\RecruitCandidateMap;
$candidate = new RecruitCandidateMap();
?>
<input hidden class="menu" value='<?= Json::encode([
    'menu-1-com',
    'menu-2-recruitment'
])?>'>
<div class="col-md-12" style="height: 100%">
    <ul class="nav nav-tabs">
        <li class="<?= (!isset($tab) || $tab === 'candidate-list')?'active':''?>"><a href="#candidate-list" data-toggle="tab">候选人</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade <?= (!isset($tab) || $tab === 'candidate-list')?'active in':''?>" id="candidate-list">
            <?= $this->render('list-panel',[
                'recruit_uuid'=>$recruit_uuid,
                'candidateList'=>isset($candidateList)?$candidateList:$candidate->getCandidateListByRecruitUuid($recruit_uuid),
                'ser_filter'=>isset($ser_filter)?$ser_filter:'',
            ])?>
        </div>
    </div>
</div>
