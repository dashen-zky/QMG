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
    'menu-1-customer',
    'menu-2-config'
])?>'>
<div class="col-md-12 configContainer">

        <ul class="nav nav-tabs">
            <li class="active"><a href="#default-tab-1" data-toggle="tab">客户类别</a></li>
            <li class=""><a href="#default-tab-2" data-toggle="tab">客户来源</a></li>
            <li class=""><a href="#default-tab-3" data-toggle="tab">意向度</a></li>
            <li class=""><a href="#default-tab-7" data-toggle="tab">客户行业</a></li>
            <li class=""><a href="#default-tab-8" data-toggle="tab">业务板块</a></li>
            <li class=""><a href="#default-tab-9" data-toggle="tab">跟进方式</a></li>
            <li class=""><a href="#default-tab-10" data-toggle="tab">跟进结果</a></li>
            <li class=""><a href="#default-tab-11" data-toggle="tab">城市</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade active in" id="default-tab-1">
                <?= $this->render('config-panel',[
                    'config'=>$config,
                    'panelTitle'=>'客户类别',
                    'key'=>'type',
                ])?>
            </div>
            <div class="tab-pane fade" id="default-tab-2">
                <?= $this->render('config-panel',[
                    'config'=>$config,
                    'panelTitle'=>'客户来源',
                    'key'=>'from',
                ])?>
            </div>
            <div class="tab-pane fade" id="default-tab-3">
                <?= $this->render('config-panel',[
                    'config'=>$config,
                    'panelTitle'=>'意向度',
                    'key'=>'internLevel',
                ])?>
            </div>
            <div class="tab-pane fade" id="default-tab-7">
                <?= $this->render('config-panel',[
                    'config'=>$config,
                    'panelTitle'=>'客户行业',
                    'key'=>'industry',
                ])?>
            </div>
            <div class="tab-pane fade" id="default-tab-8">
                <?= $this->render('config-panel',[
                    'config'=>$config,
                    'panelTitle'=>'业务板块',
                    'key'=>'business',
                ])?>
            </div>
            <div class="tab-pane fade" id="default-tab-9">
                <?= $this->render('config-panel',[
                    'config'=>$config,
                    'panelTitle'=>'跟进方式',
                    'key'=>'touchType',
                ])?>
            </div>
            <div class="tab-pane fade" id="default-tab-10">
                <?= $this->render('config-panel',[
                    'config'=>$config,
                    'panelTitle'=>'跟进结果',
                    'key'=>'touchResult',
                ])?>
            </div>
            <div class="tab-pane fade" id="default-tab-11">
                <?= $this->render('config-panel',[
                    'config'=>$config,
                    'panelTitle'=>'城市',
                    'key'=>'city',
                ])?>
            </div>
        </div>
</div>