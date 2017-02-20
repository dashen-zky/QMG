<?php
use yii\helpers\Json;
use backend\modules\hr\recruitment\models\Candidate;
use backend\modules\hr\recruitment\models\CandidateConfig;
$candidate = new Candidate();
?>
<input hidden class="menu" value='<?= Json::encode([
    'menu-1-hr',
    'menu-2-candidate',
])?>'>
<div class="col-md-12" style="height: 100%">
    <ul class="nav nav-tabs">
        <li class="<?= (!isset($tab) || $tab === 'candidate-list')?'active':''?>"><a href="#candidate-list" data-toggle="tab">简历库</a></li>
        <li class="<?= (!isset($tab) || $tab === 'black-list')?'active':''?>"><a href="#black-list" data-toggle="tab">黑名单</a></li>
        <li class="<?= (isset($tab) && $tab === 'add')?'active':''?>"><a href="#add" data-toggle="tab">添加简历</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade <?= (!isset($tab) || $tab === 'candidate-list')?'active in':''?>" id="candidate-list">
            <?= $this->render('list-panel',[
                'candidateList'=>isset($candidateList)?$candidateList:$candidate->candidateList([
                    'candidate'=>[
                        '*'
                    ],
                ],[
                    '<>',
                    Candidate::$aliasMap['candidate'] . '.location',
                    CandidateConfig::LocateBlackList
                ]),
                'ser_filter'=>isset($ser_filter)?$ser_filter:serialize([
                    '<>',
                    Candidate::$aliasMap['candidate'] . '.location',
                    CandidateConfig::LocateBlackList,
                ]),
            ])?>
        </div>
        <div class="tab-pane fade <?= (!isset($tab) || $tab === 'black-list')?'active in':''?>" id="black-list">
            <?= $this->render('black-list-panel',[
                'candidateList'=>isset($blackList)?$blackList:$candidate->candidateList(
                    [
                        'candidate'=>[
                            '*'
                        ],
                    ],
                    [
                        '=',
                        Candidate::$aliasMap['candidate'] . '.location',
                        CandidateConfig::LocateBlackList
                    ]
                ),
                'ser_filter'=>isset($black_list_ser_filter)?$black_list_ser_filter:serialize([
                    'location'=>CandidateConfig::LocateBlackList,
                ]),
            ])?>
        </div>
        <div class="tab-pane fade <?= (isset($tab) && $tab === 'add')?'active in':''?>" id="add">
            <?= $this->render('add')?>
        </div>
    </div>
</div>
