<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/3 0003
 * Time: 上午 1:43
 */
?>
<?php
use yii\helpers\Json;
use backend\modules\hr\models\EmployeeBasicInformation;
$employee = new EmployeeBasicInformation();
?>
<input hidden class="menu" value='<?= Json::encode([
    'menu-1-hr',
    'menu-2-employee',
])?>'>
<div class="col-md-12" style="height: 100%">
    <ul class="nav nav-tabs">
        <li class="<?= (!isset($tab) || $tab === 'working-list')?'active':''?>"><a href="#default-tab-working-list" data-toggle="tab">在职员工</a></li>
        <li class="<?= (isset($tab) && $tab === 'disabled-list')?'active':''?>"><a href="#default-tab-disabled-list" data-toggle="tab">离职员工</a></li>
        <li class="<?= (isset($tab) && $tab === 'waiting-list')?'active':''?>"><a href="#default-tab-waiting-list" data-toggle="tab">待入职</a></li>
        <li class="<?= (isset($tab) && $tab === 'add-employee')?'active':''?>"><a href="#default-tab-add" data-toggle="tab">添加员工</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade <?= (!isset($tab) || $tab === 'working-list')?'active in':''?>" id="default-tab-working-list">
            <?= $this->render('employee-list',[
                'employeeList' => isset($workingEmployeeList)?$workingEmployeeList:$employee->workingList(),
                'ser_filter'=>isset($working_ser_filter)?$working_ser_filter:null,
                'positionList'=>isset($workingPositionList)?$workingPositionList:[],
                'entrance'=>'working'
            ])?>
        </div>
        <div class="tab-pane fade <?= (!isset($tab) && $tab === 'disabled-list')?'active in':''?>" id="default-tab-disabled-list">
            <?= $this->render('disabled-employee-list',[
                'employeeList' => isset($disabledEmployeeList)?$disabledEmployeeList : $employee->disabledList(),
                'ser_filter'=>isset($disabled_ser_filter)?$disabled_ser_filter:null,
                'positionList'=>isset($disabledPositionList)?$disabledPositionList : [],
                'entrance'=>'disabled',
            ])?>
        </div>
        <div class="tab-pane fade <?= (!isset($tab) && $tab === 'waiting-list')?'active in':''?>" id="default-tab-waiting-list">
            <?= $this->render('employee-list',[
                'employeeList' => isset($waitingEmployeeList)?$waitingEmployeeList : $employee->waitingList(),
                'ser_filter'=>isset($waiting_ser_filter)?$waiting_ser_filter:null,
                'positionList'=>isset($waitingPositionList)?$waitingPositionList : [],
                'entrance'=>'waiting',
            ])?>
        </div>
        <div class="tab-pane fade <?= (isset($tab) && $tab === 'add-employee')?'active in':''?>" id="default-tab-add">
            <?= $this->render('add',[
                'model'=>$model,
                'familyList'=>'',
            ])?>
        </div>
    </div>
</div>
