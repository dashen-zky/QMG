<!-- begin panel -->
<?php
use yii\widgets\Pjax;
use backend\modules\hr\models\EmployeeAccount;
use yii\helpers\Url;
use yii\web\View;
use backend\modules\hr\models\EmployeeForm;
use backend\models\MyLinkPage;
?>
<div class="panel-body employee-list">
    <?php Pjax::begin()?>
<?php 
$JS = <<<JS
$(function() {
$('.employee-list').on('click','.show-employee',function() {
    var url = $(this).attr('url');
    window.open(url); 
});
// 入职清单
$('.employee-list').on('click', '.entry-list', function() {
    var self = $(this);
    var url = self.attr('url');
    $.get(
    url,
    function(data, status) {
        if(status === 'success') {
            var modal = self.parents('.panel-body').find('.entry-list-modal');
            modal.find('.modal-body').html(data);
            modal.modal('show');
            
            modal.on('click','.editForm',function() {
                var enableEditField = modal.find('.enableEdit');
                enableEditField.attr("disabled",false);
                var enableEditBlock = modal.find('.enableEditBlock');
                enableEditBlock.css('display','block');
            });
        }
    });
});
// 离职清单
$('.employee-list').on('click', '.dismiss-list', function() {
    var self = $(this);
    var url = self.attr('url');
    $.get(
    url,
    function(data, status) {
        if(status === 'success') {
            var modal = self.parents('.panel-body').find('.dismiss-list-modal');
            modal.find('.modal-body').html(data);
            modal.modal('show');
            
            modal.on('click','.editForm',function() {
                var enableEditField = modal.find('.enableEdit');
                enableEditField.attr("disabled",false);
                var enableEditBlock = modal.find('.enableEditBlock');
                enableEditBlock.css('display','block');
            });
        }
    });
});
});
JS;
$this->registerJs($JS, View::POS_END);
?>
    <?= $this->render('list-filter-form',[
        'formData'=>unserialize(isset($ser_filter)?$ser_filter:''),
        'positionList'=>$positionList,
        'entrance'=>$entrance,
    ]);?>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>#</th>
            <th>姓名</th>
            <th>职位</th>
            <th>部门</th>
            <th>所属公司</th>
            <th>薪资</th>
            <th>入职日期</th>
            <th>转正日期</th>
            <th>电话/邮箱</th>

            <th>操作</th>
        </tr>
        </thead>
        <tbody>
            <?php
                $degreeList = EmployeeForm::educationDegreeList();
                $statusList = EmployeeForm::employeeStatusList();
                $typeList = EmployeeForm::employeeTypeList();
            ?>
            <?php foreach($employeeList['employeeList'] as $item):?>
                <tr>
                    <td>
                        <div><?= $item['system_code']?></div>
                    </td>
                    <td>
                        <?php if ($item['status'] == EmployeeAccount::STATUS_LEAVED) :?>
                            <a url="<?= Url::to([
                                '/hr/employee/edit',
                                'uuid'=>$item['uuid']])?>"
                               class="show-employee" href="#">
                                <s><?= $item['name']?>/<?= $item['english_name']?></s>
                            </a>
                        <?php else :?>
                            <a url="<?= Url::to([
                                '/hr/employee/edit',
                                'uuid'=>$item['uuid']])?>"
                               class="show-employee" href="#">
                                <?= $item['name']?>/<?= $item['english_name']?>
                            </a>
                        <?php endif;?>
                    </td>
                    <td>
                        <?php if(!empty($item['position_name'])) :?>
                            <?php $position_names = explode(',',$item['position_name'])?>
                            <?php foreach($position_names as $position_name):?>
                                <div><?= $position_name?></div>
                            <?php endforeach?>
                        <?php endif?>
                    </td>
                    <td>
                        <?php if(!empty($item['department_name'])) :?>
                            <?php $department_names = explode(',',$item['department_name'])?>
                            <?php foreach($department_names as $department_name):?>
                                <div><?= $department_name?></div>
                            <?php endforeach?>
                        <?php endif?>
                    </td>
                    <td>
                        <?php if(!empty($item['company_name'])) :?>
                            <?php $company_names = explode(',',$item['company_name'])?>
                            <?php foreach($company_names as $company_name):?>
                                <div><?= $company_name?></div>
                            <?php endforeach?>
                        <?php endif?>
                    </td>
                    <td><?= $item['salary']?></td>
                    <td><?= (isset($item['entry_time']) && $item['entry_time'] !='0')?date("Y-m-d", $item['entry_time']):''?></td>
                    <td><?= (isset($item['become_full_member_time']) && $item['become_full_member_time'] !='0')?date("Y-m-d", $item['become_full_member_time']):''?></td>
                    <td>
                        <div><?= $item['phone_number']?></div>
                        <div><?= $item['work_email']?></div>
                    </td>
                    <td>
                        <div class="btn-group m-r-5 m-b-5">
                            <a href="javascript:;" data-toggle="dropdown" class="btn btn-success dropdown-toggle" aria-expanded="false">
                                <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu pull-right">
                                <li></li>
                                <?php if(isset($entrance) && in_array($entrance, [
                                        'waiting',
                                        'working'
                                    ])) :?>
                                <li><a url="<?= Url::to([
                                        '/hr/employee/entry-list',
                                        'uuid'=>$item['uuid']
                                    ])?>" href="#" class="entry-list">入职清单</a></li>
                                <?php endif;?>
                                <?php if(isset($entrance) && $entrance == 'working') :?>
                                <li><a url="<?= Url::to([
                                        '/hr/employee/dismiss-list',
                                        'uuid'=>$item['uuid']
                                    ])?>" href="#" class="entry-list">离职</a></li>
                                <?php endif;?>
                            </ul>
                        </div>
                    </td>
                </tr>
            <?php endforeach?>
        </tbody>
    </table>
    <?php
        if(isset($ser_filter) && !empty($ser_filter)) {
            $pageParams = [
                'pagination' => $employeeList['pagination'],
                'ser_filter'=>$ser_filter,
            ];
        } else {
            $pageParams = [
                'pagination' => $employeeList['pagination'],
            ];
        }
    ?>
    <?= MyLinkPage::widget($pageParams); ?>
    <?php Pjax::end()?>
    <?= $this->render('/employee-entry/entry-list-modal',[
        'edit'=>true,
    ])?>
    <?= $this->render('/employee-dismiss/dismiss-list-modal',[
        'edit'=>true,
    ])?>
</div>
<!-- end panel -->