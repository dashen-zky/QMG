<?php
use yii\web\View;
use yii\helpers\Json;
$JS = <<<JS
$(function() {
    $('table.config-table').on("click",".removeRow",function() {
        $(this).parentsUntil('tbody').remove();
    });
    $('table.config-table').on('click',' .addRow',function() {
        var name = $(this).attr('name');
        var index = (new Date()).getTime();

        var html = '<tr>' +
         '<td><input class="index form-control" name=Config['+name+']['+index+'][key] value='+index+' readonly></td>' +
          '<td><input class="form-control" name=Config['+name+']['+index+'][value]></td>' +
          '<td>' +
           '<button type="button" class="btn-xs btn-primary removeRow">' +
            '<i class="fa fa-2x fa-minus"></i>' +
             '</button></td></tr>';
        var parent = $(this).parent().parent();
        parent.before(html).fadeIn();
    });
})

JS;
$this->registerJs($JS, View::POS_END);
?>
<input hidden class="menu" value='<?= Json::encode([
    'menu-1-hr',
    'menu-2-config',
    'menu-3-entry'
])?>'>
<div class="col-md-12 configContainer">

        <ul class="nav nav-tabs">
            <li class="<?= !isset($tab) || $tab == 'tab-1'?'active':''?>"><a href="#default-tab-1" data-toggle="tab">个人资料</a></li>
            <li class="<?= isset($tab) && $tab == 'tab-2'?'active':''?>"><a href="#default-tab-2" data-toggle="tab">入职阅知</a></li>
            <li class="<?= isset($tab) && $tab == 'tab-3'?'active':''?>"><a href="#default-tab-3" data-toggle="tab">劳动合同</a></li>
            <li class="<?= isset($tab) && $tab == 'tab-5'?'active':''?>"><a href="#default-tab-5" data-toggle="tab">培训</a></li>
            <li class="<?= isset($tab) && $tab == 'tab-6'?'active':''?>"><a href="#default-tab-6" data-toggle="tab">办公物品</a></li>
            <li class="<?= isset($tab) && $tab == 'tab-7'?'active':''?>"><a href="#default-tab-7" data-toggle="tab">工作通讯</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade <?= !isset($tab) || $tab == 'tab-1'?'active in':''?>" id="default-tab-1">
                <?= $this->render('config-panel',[
                    'config'=>$config,
                    'panelTitle'=>'个人资料',
                    'key'=>'basic_information',
                    'tab'=>'tab-1'
                ])?>
            </div>
            <div class="tab-pane fade <?= isset($tab) && $tab == 'tab-2'?'active in':''?>" id="default-tab-2">
                <?= $this->render('config-panel',[
                    'config'=>$config,
                    'panelTitle'=>'入职阅知',
                    'key'=>'entry_regulation_list',
                    'tab'=>'tab-2'
                ])?>
            </div>
            <div class="tab-pane fade <?= isset($tab) && $tab == 'tab-3'?'active in':''?>" id="default-tab-3">
                <?= $this->render('config-panel',[
                    'config'=>$config,
                    'panelTitle'=>'劳动合同',
                    'key'=>'contract',
                    'tab'=>'tab-3'
                ])?>
            </div>
            <div class="tab-pane fade <?= isset($tab) && $tab == 'tab-5'?'active in':''?>" id="default-tab-5">
                <?= $this->render('config-panel',[
                    'config'=>$config,
                    'panelTitle'=>'培训',
                    'key'=>'training',
                    'tab'=>'tab-5'
                ])?>
            </div>
            <div class="tab-pane fade <?= isset($tab) && $tab == 'tab-6'?'active in':''?>" id="default-tab-6">
                <?= $this->render('config-panel',[
                    'config'=>$config,
                    'panelTitle'=>'办公物品',
                    'key'=>'office_assets',
                    'tab'=>'tab-6'
                ])?>
            </div>
            <div class="tab-pane fade <?= isset($tab) && $tab == 'tab-7'?'active in':''?>" id="default-tab-7">
                <?= $this->render('config-panel',[
                    'config'=>$config,
                    'panelTitle'=>'工作通讯',
                    'key'=>'communication',
                    'tab'=>'tab-7'
                ])?>
            </div>
        </div>
</div>