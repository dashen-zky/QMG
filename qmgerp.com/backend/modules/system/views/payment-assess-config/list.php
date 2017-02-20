<?= $this->render('@webroot/../views/site/panel-header',[
    'title'=>'配置列表',
    'class'=>'col-md-6'
])?>
<?php
use yii\helpers\Url;
?>
<table class="table">
    <thead>
    <th>条件类型</th>
    <th>条件</th>
    <th>审核人</th>
    <th>操作</th>
    </thead>
    <tbody>
    <?php if(!empty($config)) :?>
        <?php foreach($config as $index=>$item) :?>
            <?php if($item['step'] !== $step) :?>
                <?php continue;?>
            <?php endif?>
            <tr>
                <td><?= isset($item['condition_type'])?$item['condition_type']:''?></td>
                <td><?= isset($item['condition_item'])?$item['condition_item']:''?></td>
                <td><?= isset($item['assess_name'])?$item['assess_name']:''?></td>
                <td><a class="btn" href="<?= Url::to([
                        $itemDel,
                        'id'=>$index,
                        'tab'=>$step,
                    ])?>">删除</a></td>
            </tr>
        <?php endforeach?>
    <?php endif?>
    </tbody>
</table>
<?= $this->render('@webroot/../views/site/panel-footer')?>
