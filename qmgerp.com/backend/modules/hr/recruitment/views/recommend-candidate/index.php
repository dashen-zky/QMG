<?php
use yii\helpers\Json;
use backend\modules\hr\recruitment\models\ApplyRecruit;
use backend\modules\hr\recruitment\models\ApplyRecruitConfig;
$applyRecruit = new ApplyRecruit();
?>
<input hidden class="menu" value='<?= Json::encode([
    'menu-1-hr',
    'menu-2-recommend-candidate',
])?>'>
<div class="col-md-12" style="height: 100%">
    <ul class="nav nav-tabs">
        <li class="<?= (!isset($tab) || $tab === 'list')?'active':''?>"><a href="#list" data-toggle="tab">招聘列表</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade <?= (!isset($tab) || $tab === 'list')?'active in':''?>" id="list">
            <?= $this->render('list', [
                'applyRecruitList'=>isset($applyRecruitList)?$applyRecruitList:$applyRecruit->applyRecruitList([
                    'apply_recruit'=>[
                        '*'
                    ],
                    'created'=>[
                        'name'
                    ],
                    'position'=> [
                        'name',
                        'requirement',
                    ]
                ],[
                    '=',
                    ApplyRecruit::$aliasMap['apply_recruit'] . '.status',
                    ApplyRecruitConfig::StatusRecruiting,
                ]),
                'ser_filter'=>isset($ser_filter)?$ser_filter:'',
            ])?>
        </div>
    </div>
</div>
