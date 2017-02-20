<?php
use yii\helpers\Json;
use backend\modules\hr\recruitment\models\Candidate;
use backend\modules\hr\recruitment\models\CandidateConfig;
$candidate = new Candidate();
?>
<input hidden class="menu" value='<?= Json::encode([
    'menu-1-hr',
    'menu-2-recommend-candidate',
])?>'>
<div class="col-md-12" style="height: 100%">
    <ul class="nav nav-tabs">
        <li class="<?= (!isset($tab) || $tab === 'candidate-list')?'active':''?>"><a href="#candidate-list" data-toggle="tab">候选人</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade <?= (!isset($tab) || $tab === 'candidate-list')?'active in':''?>" id="candidate-list">
            <?= $this->render('select-candidate-panel',[
                'recruit_uuid'=>$recruit_uuid,
                'candidateList'=>isset($candidateList)?$candidateList:$candidate->candidateList([
                    'candidate'=>[
                        '*'
                    ]
                ], [
                    'not in',
                    Candidate::$aliasMap['candidate'] . '.location',
                    [
                        CandidateConfig::LocateBlackList,
                        CandidateConfig::LocateHired,
                    ]
                ]),
                'ser_filter'=>isset($ser_filter)?$ser_filter:'',
            ])?>
        </div>
    </div>
</div>
