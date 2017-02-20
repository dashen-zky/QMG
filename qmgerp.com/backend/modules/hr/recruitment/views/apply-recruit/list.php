<?php
use yii\widgets\Pjax;
use backend\models\MyLinkPage;
use yii\helpers\Url;
use backend\modules\hr\recruitment\models\ApplyRecruitConfig;
$config = new ApplyRecruitConfig();
?>
<div class="panel apply-recruit-list">
<?php Pjax::begin(); ?>
<?php
$Js = <<<JS
$(function() {
$(".datetimepicker").datetimepicker({
    lang:"ch",           //语言选择中文
    format:"Y-m-d",      //格式化日期H:i
    i18n:{
      // 以中文显示月份
      de:{
        months:["1月","2月","3月","4月","5月","6月","7月","8月","9月","10月","11月","12月",],
        // 以中文显示每周（必须按此顺序，否则日期出错）
        dayOfWeek:["日","一","二","三","四","五","六"]
      }
    }
    // 显示成年月日，时间--
});

$('.apply-recruit-list').on('click','.show', function() {
    var self = $(this);
    $.get(self.attr('url'), function(data, status) {
        if(status !== 'success') {
            return ;
        }
        
        var modal = self.parents('.panel').find('.apply-recruit-show');
        modal.find('.modal-body').html(data);
        modal.modal('show');
        
        modal.on('click','.editForm',function() {
            modal.find('.enableEdit').attr("disabled",false);
            modal.find('.displayBlockWhileEdit').css('display','block');
        });
        
        var form = modal.find('.ApplyRecruitForm');
        form.on('click','.submit', function() {
            var form = $(this).parents('form');
            var number_of_plan = form.find('.number_of_plan').val();
            var rest_number = form.find('.rest_number').val();
            if (number_of_plan > rest_number || number_of_plan == 0) {
                form.find('.number_of_plan_error').css('display','block');
                return false;
            }
            
            var position_uuid = form.find('.position_uuid').val();
            if (position_uuid == 0) {
                form.find('.position_uuid_error').css('display','block');
                return false;
            }
            
            form.submit();
        });
        
        $(".datetimepicker").datetimepicker({
            lang:"ch",           //语言选择中文
            format:"Y-m-d",      //格式化日期H:i
            i18n:{
              // 以中文显示月份
              de:{
                months:["1月","2月","3月","4月","5月","6月","7月","8月","9月","10月","11月","12月",],
                // 以中文显示每周（必须按此顺序，否则日期出错）
                dayOfWeek:["日","一","二","三","四","五","六"]
              }
            }
            // 显示成年月日，时间--
        });
    });
});
});
JS;
$this->registerJs($Js, \yii\web\View::POS_END);
?>
<?= $this->render('list-filter-form',[
    'formData'=>unserialize(isset($ser_filter)?$ser_filter:''),
    'action'=>['/recruitment/apply-recruit/list-filter'],
]);?>
<table class="table">
    <thead>
    <tr>
        <th>#</th>
        <th>岗位</th>
        <th>招聘数量</th>
        <th>创建人</th>
        <th>状态</th>
        <th class="col-md-4">岗位要求</th>
        <th class="col-md-4">备注</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($applyRecruitList['list'] as $item) :?>
        <tr>
            <td><?= $item['id']?></td>
            <td>
                <a url="<?= Url::to([
                    '/recruitment/apply-recruit/show',
                    'uuid'=>$item['uuid'],
                    'edit'=>true,
                ])?>" class="show" href="#">
                    <?= $item['position_name']?>
                </a>
            </td>
            <td><?= $item['number_of_plan']?></td>
            <td><?= $item['created_name']?></td>
            <td><?= $config->getAppointed('status', $item['status'])?></td>
            <td><?= $item['position_requirement']?></td>
            <td><?= $item['remarks']?></td>
            <td>
            <?php if(in_array($item['status'], [
                ApplyRecruitConfig::StatusApplying,
                ApplyRecruitConfig::StatusAssessRefused
            ])) :?>
                <a href="<?= Url::to([
                    '/recruitment/apply-recruit/del',
                    'uuid'=>$item['uuid'],
                ])?>">删除</a>
            <?php endif;?>
            <?php if($item['status'] == ApplyRecruitConfig::StatusRecruiting) :?>
                <div>
                    <a href="<?= Url::to([
                        '/recruitment/apply-recruit/achieved',
                        'uuid'=>$item['uuid'],
                    ])?>">完成</a>
                </div>
                <div>
                    <a url='<?= Url::to([
                        '/recruitment/recruitment-candidate/index',
                        'uuid'=>$item['uuid'],
                    ])?>' class="show-new-tab" href="#">候选人</a>
                </div>
            <?php endif;?>
            </td>
        </tr>
    <?php endforeach?>
    </tbody>
</table>
<?php
if(isset($ser_filter) && !empty($ser_filter)) {
    $pageParams = [
        'pagination' => $applyRecruitList['pagination'],
        'ser_filter'=>$ser_filter,
    ];
} else {
    $pageParams = [
        'pagination' => $applyRecruitList['pagination'],
    ];
}
?>
<?= MyLinkPage::widget($pageParams); ?>
<?php Pjax::end(); ?>
<?= $this->render('show-modal')?>
</div>
