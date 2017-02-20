<?php
use yii\widgets\Pjax;
use backend\models\MyLinkPage;
use yii\helpers\Url;
?>
<!-- begin panel -->
<div class="panel panel-body anniversary-achievement-list">
    <?php Pjax::begin(); ?>
    <?= $this->render('anniversary-achievement-list-filter-form',[
        'formData'=>isset($ser_filter)?unserialize($ser_filter):'',
    ])?>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>姓名</th>
            <th>部门</th>
            <th>岗位</th>
            <th>客户总数</th>
            <th>年</th>
            <th>年度目标</th>
            <th>季度目标</th>
            <th>完成金额</th>
            <th>开票金额</th>
            <th>已收回金额</th>
            <th>完成进度</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($anniversaryAchievementList['list'] as $item) :?>
            <tr>
                <td><a url="<?= Url::to([
                        '/crm/private-customer/index',
                        'ser_filter'=>serialize(['sales_name'=>$item['sales_name']])
                    ])?>" href="#" class="show-new-tab"><?= $item['sales_name']?></a></td>
                <td>
                    <?php if(!empty($item['department_name'])) :?>
                        <?php $department_names = explode(',',$item['department_name'])?>
                        <?php foreach($department_names as $department_name):?>
                            <div><?= $department_name?></div>
                        <?php endforeach?>
                    <?php endif?>
                </td>
                <td>
                    <?php if(!empty($item['position_name'])) :?>
                        <?php $position_names = explode(',',$item['position_name'])?>
                        <?php foreach($position_names as $position_name):?>
                            <div><?= $position_name?></div>
                        <?php endforeach?>
                    <?php endif?>
                </td>
                <td><?= $item['customer_total']?></td>
                <td><?= $item['year']?></td>
                <td><?= $item['anniversary_target']?></td>
                <td>
                    <div>Q1:<?= $item['m1_target'] + $item['m2_target'] + $item['m3_target']?></div>
                    <div>Q2:<?= $item['m4_target'] + $item['m5_target'] + $item['m6_target']?></div>
                    <div>Q3:<?= $item['m7_target'] + $item['m8_target'] + $item['m9_target']?></div>
                    <div>Q4:<?= $item['m10_target'] + $item['m11_target'] + $item['m12_target']?></div>
                </td>
                <td><?= $item['achieved']?></td>
                <td><?= $item['checked_stamp_money']?></td>
                <td><?= $item['received_money']?></td>
                <td>
                    <?php if($item['anniversary_target'] == 0) :?>
                        <?= 0?>
                    <?php else:?>
                        <?= sprintf("%.2f", $item['achieved']/$item['anniversary_target'])?>
                    <?php endif;?>
                </td>
                <td>
                    <div>
                        <a url="<?= Url::to([
                            '/statistic/sales-statistic/edit-anniversary-achievement',
                            'uuid'=>$item['uuid'],
                        ])?>" class="sales-target" href="#">设定销售目标</a>
                    </div>
                </td>
            </tr>
        <?php endforeach?>
        </tbody>
    </table>
    <?php
    if(isset($ser_filter) && !empty($ser_filter)) {
        $pageParams = [
            'pagination' => $anniversaryAchievementList['pagination'],
            'ser_filter'=>$ser_filter,
        ];
    } else {
        $pageParams = [
            'pagination' => $anniversaryAchievementList['pagination'],
        ];
    }
    ?>
    <?= MyLinkPage::widget($pageParams); ?>
    <?php Pjax::end(); ?>
    <?= $this->render('anniversary-achievement-modal',[
        'edit'=>true,
    ])?>
</div>
<!-- end panel -->
<?php
$JS = <<<JS
$('.anniversary-achievement-list').on('click','.sales-target', function() {
    var self = $(this);
    var url = self.attr('url');
    $.get(url, function(data, status) {
        if(status !== 'success') {
            return ;
        }
        
        var panel = self.parents('.panel');
        var modal = panel.find('.anniversary-achievement-modal');
        modal.find('.modal-body').html(data);
        modal.modal('show');
        
        panel.on('click','.editForm',function() {
            panel.find('.enableEdit').attr("disabled",false);
            panel.find('.displayBlockWhileEdit').css('display','block');
        });
    });
});
JS;
$this->registerJs($JS, \yii\web\View::POS_END);
?>