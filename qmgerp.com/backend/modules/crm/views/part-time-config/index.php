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
           '<button type="button" class="btn btn-primary removeRow">' +
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
<?php use yii\helpers\Json;?>
<input hidden class="menu" value='<?= Json::encode([
    'menu-1-supplier',
    'menu-2-config',
    'menu-3-part-time',
])?>'>
<div class="col-md-12 configContainer">

        <ul class="nav nav-tabs">
            <li class="<?= (!isset($tab) || $tab === 'status')?'active':''?>"><a href="#part-time-config-tab-1" data-toggle="tab">状态</a></li>
            <li class="<?= (isset($tab) && $tab === 'position')?'active':''?>"><a href="#part-time-config-tab-3" data-toggle="tab">职能</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade <?= (!isset($tab) || $tab === 'status')?'active in':''?>" id="part-time-config-tab-1">
                <?= $this->render('config-panel',[
                    'config'=>$config,
                    'panelTitle'=>'状态',
                    'key'=>'status',
                    'editKey'=>false,
                ])?>
            </div>
            <div class="tab-pane fade <?= (isset($tab) && $tab === 'position')?'active in':''?>" id="part-time-config-tab-3">
                <?= $this->render('config-panel',[
                    'config'=>$config,
                    'panelTitle'=>'职能',
                    'key'=>'position',
                    'editKey'=>false,
                ])?>
            </div>
        </div>
</div>