<?php
use yii\widgets\Pjax;
use backend\models\MyLinkPage;
use yii\helpers\Url;
use yii\web\View;
$contractConfig = new \backend\modules\fin\models\contract\ContractConfig();
if (!isset($enableEdit)) {
    $enableEdit = true;
}
?>
<div class="panel-body">
<?php Pjax::begin(); ?>
    <?php
    $JS = <<<JS
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
});
JS;
    $this->registerJs($JS, View::POS_END);?>
    <?php if(isset($need_filter) && $need_filter) :?>
    <?= $this->render('list-filter-form',[
        'formData'=>unserialize(isset($ser_filter)?$ser_filter:''),
    ]);?>
    <?php endif;?>
    <div class="panel-body">
        <table class="table table-striped customer-contract-list">
            <thead>
            <tr>
                <th>客户名称</th>
                <th>合同编号</th>
                <th>合同负责人</th>
                <th>合同状态</th>
                <th>签订时间</th>
                <th>合同金额</th>
                <?php if($enableEdit) {?>
                    <th>操作</th>
                <?php }?>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($contractList['list'] as $item) :?>
                <tr>
                    <td><?= $item['customer_name']?></td>
                    <td>
                        <a class="showCustomerContract"
                           name="<?= Url::to([
                               '/crm/customer-contract/edit',
                               'uuid'=>$item['uuid'],
                               'object_uuid'=>$item['customer_uuid'],
                               'back_url'=>isset($back_url)?$back_url:null,
                               'enableEdit'=>$enableEdit,
                           ])?>" href="#"><?= $item['type'].$item['code']?></a>
                    </td>
                    <td><?= $item['duty_name']?></td>
                    <td><?= $contractConfig->getAppointed('status',$item['status'])?></td>
                    <td><?= ($item['sign_time'] != 0)?date("Y-m-d",$item['sign_time']):''?></td>
                    <td><?= $item['money']?></td>
                    <?php if($enableEdit) {?>
                        <td>
                            <a href="<?= Url::to([
                                '/crm/customer-contract/del',
                                'uuid'=>$item['uuid'],
                                'object_uuid'=>$item['customer_uuid'],
                                'back_url'=>isset($back_url)?$back_url:null,
                            ])?>">删除</a>
                        </td>
                    <?php }?>
                </tr>
            <?php endforeach?>
            </tbody>
        </table>
        <?php
        if(isset($ser_filter) && !empty($ser_filter)) {
            $pageParams = [
                'pagination' => $contractList['pagination'],
                'ser_filter'=>$ser_filter,
            ];
        } else {
            $pageParams = [
                'pagination' => $contractList['pagination'],
            ];
        }
        ?>
        <?= MyLinkPage::widget($pageParams); ?>
    </div>
<?php Pjax::end(); ?>
</div>
<?= $this->render('edit',[
])?>
<?php
$JS = <<<JS
$(function() {
$('.customer-contract-list .showCustomerContract').click(function() {
    var url = $(this).attr('name');
    $.get(
    url,
    function(data, status) {
        if(status === 'success') {
            var modal = $('.edit-customer-contract');
            modal.find('.modal-body').html(data);
            modal.modal('show');
            $("form").on('click','.editForm',function() {
                var form = $(this).parents('form');
                form.find('.enableEdit').attr("disabled",false);
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
            // 附件删除js
             $('.CustomerContractForm .customer-contract-table').on('click','.attachmentDelete',function() {
                var url = $(this).attr('name');
                var self = $(this);
                $.get(
                url,
                function(data,status) {
                    if('success' == status) {
                        if(data) {
                            self.parentsUntil('td').remove();
                        }
                    }
                });
             });
        }
    }
    )
});
})
JS;
$this->registerJs($JS, \yii\web\View::POS_END);
?>
