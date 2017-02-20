<?php
use yii\widgets\Pjax;
use yii\widgets\LinkPager;
use yii\helpers\Url;
?>
<div class="panel-body">
<?php Pjax::begin(); ?>
    <div class="panel-body supplier-contract-list">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>供应商名称</th>
                <th>合同编号</th>
                <th>合同负责人</th>
                <th>合同状态</th>
                <th>创建时间</th>
                <th>合同金额</th>
                <?php if(isset($operator)) {?>
                    <th>操作</th>
                <?php }?>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($contractList['list'] as $item) :?>
                <tr>
                    <td><?= $item['supplier_name']?></td>
                    <td><?= $item['type'].$item['code']?></td>
                    <td><?= $item['duty_name']?></td>
                    <td><?= $model->getAppointed('status',$item['status'])?></td>
                    <td><?= ($item['create_time'] != 0)?date("Y-m-d",$item['create_time']):''?></td>
                    <td><?= $item['money']?></td>
                    <?php if(isset($operator)) {?>
                        <td>
                            <div class="btn-group m-r-5 m-b-5">
                                <a href="javascript:;" data-toggle="dropdown" class="btn btn-success dropdown-toggle" aria-expanded="false">
                                    <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu pull-right">
                                    <li><a class="showSupplierContract"
                                           name="<?= Url::to([
                                               '/crm/supplier-contract/edit',
                                               'uuid'=>$item['uuid'],
                                               'object_uuid'=>$object_uuid,
                                           ])?>">查看</a></li>
                                    <li>
                                        <a href="<?= Url::to([
                                            '/crm/supplier-contract/del',
                                            'uuid'=>$item['uuid'],
                                            'object_uuid'=>$object_uuid,
                                        ])?>">删除</a></li>
                                </ul>
                            </div>
                        </td>
                    <?php }?>
                </tr>
            <?php endforeach?>
            </tbody>
        </table>
        <?= LinkPager::widget(['pagination' => $contractList['pagination']]); ?>
    </div>
<?php Pjax::end(); ?>
</div>
<?= $this->render('edit',[])?>
<?php
$JS = <<<JS
$(function() {
$('.supplier-contract-list .showSupplierContract').click(function() {
    var url = $(this).attr('name');
    $.get(
    url,
    function(data, status) {
        if(status === 'success') {
            var modal = $('.edit-supplier-contract');
            modal.find('.modal-body').html(data);
            modal.modal('show');
            $("form").on('click','.editForm',function() {
                var form = $(this).parents('form');
                form.find('.enableEdit').attr("disabled",false);
            });

            $('form').on('change','.contractType',function() {
                var code_prefix = $(this).val();
                var form = $(this).parents('form');
                var code_field = form.find('.contractCode');
                var code = code_field.val();
                code = code.replace(/[A-Z]+/,code_prefix);
                code_field.val(code);
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
             $('.SupplierContractForm .supplier-contract-table').on('click','.attachmentDelete',function() {
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
