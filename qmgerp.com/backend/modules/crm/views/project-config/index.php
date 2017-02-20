<?php use yii\helpers\Json;?>
<input hidden class="menu" value='<?= Json::encode([
    'menu-1-project',
    'menu-2-config'
])?>'>
<?php
use yii\web\View;
$JS = <<<JS
$(function() {
    $('table.config-table').on("click",".removeRow",function() {
        $(this).parentsUntil('tbody').remove();
    });
    $('table.config-table').on('click',' .addRow',function() {
        var name = $(this).attr('name');
        var editKey = $(this).attr('id') === '1';
        var index = (new Date()).getTime();
        if(editKey) {
            var html = '<tr>' +
         '<td><input class="index form-control" name=Config['+name+']['+index+'][key]></td>' +
          '<td><input class="form-control" name=Config['+name+']['+index+'][value]></td>' +
          '<td>' +
           '<button type="button" class="btn btn-primary removeRow">' +
            '<i class="fa fa-2x fa-minus"></i>' +
             '</button></td></tr>';
        } else {
            var html = '<tr>' +
         '<td><input class="index form-control" name=Config['+name+']['+index+'][key] value='+index+' readonly></td>' +
          '<td><input class="form-control" name=Config['+name+']['+index+'][value]></td>' +
          '<td>' +
           '<button type="button" class="btn btn-xs btn-primary removeRow">' +
            '<i class="fa fa-2x fa-minus"></i>' +
             '</button></td></tr>';
        }

        var parent = $(this).parent().parent();
        parent.before(html).fadeIn();
    });
})

JS;
$this->registerJs($JS, View::POS_END);
?>
<div class="col-md-12 configContainer">

        <ul class="nav nav-tabs">
            <li class="active"><a href="#default-tab-3" data-toggle="tab">回款状态</a></li>
            <li class=""><a href="#default-tab-4" data-toggle="tab">开票状态</a></li>
            <li class=""><a href="#default-tab-5" data-toggle="tab">业务板块</a></li>
            <li class=""><a href="#default-tab-6" data-toggle="tab">立项配置</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade active in" id="default-tab-3">
                <?= $this->render('config-panel',[
                    'config'=>$config,
                    'panelTitle'=>'回款状态',
                    'key'=>'receiveMoneyStatus',
                    'editKey'=>false,
                ])?>
            </div>
            <div class="tab-pane fade" id="default-tab-4">
                <?= $this->render('config-panel',[
                    'config'=>$config,
                    'panelTitle'=>'开票状态',
                    'key'=>'stampStatus',
                    'editKey'=>false,
                ])?>
            </div>
            <div class="tab-pane fade" id="default-tab-5">
                <?= $this->render('config-panel',[
                    'config'=>$config,
                    'panelTitle'=>'业务板块',
                    'key'=>'business',
                    'editKey'=>false,
                ])?>
            </div>
            <div class="tab-pane fade" id="default-tab-6">
                <?= $this->render('config-panel',[
                    'config'=>$config,
                    'panelTitle'=>'立项配置',
                    'key'=>'active',
                    'editKey'=>false,
                ])?>
            </div>
        </div>
</div>