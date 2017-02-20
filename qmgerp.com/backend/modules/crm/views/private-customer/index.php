<?php
use yii\helpers\Json;
use backend\modules\crm\models\customer\record\CustomerTouchRecordMap;
use backend\modules\crm\models\customer\model\ContactForm;
use backend\modules\crm\models\customer\record\PrivateCustomer;
use backend\modules\crm\models\customer\record\CustomerContractMap;
?>
<input hidden class="menu" value='<?= Json::encode([
    'menu-1-customer',
    'menu-2-private-customer'
])?>'>
<div class="col-md-12" style="height: 100%">
    <ul class="nav nav-tabs">
        <li class="<?= (!isset($tab) || $tab === 'customer-list')?'active':''?>"><a href="#default-tab-list" data-toggle="tab">客户列表</a></li>
        <li class="<?= (isset($tab) && $tab === 'add-customer')?'active':''?>">
            <a href="#default-tab-add" data-toggle="tab">添加客户</a></li>
        <li class="<?= (isset($tab) && $tab === 'touch-record-list')?'active':''?>">
            <a href="#default-touch-record-list" data-toggle="tab">跟进记录</a></li>
        <li class="<?= (isset($tab) && $tab === 'contract-list')?'active':''?>">
            <a href="#default-contract-list" data-toggle="tab">合同列表</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade <?= (!isset($tab) || $tab === 'customer-list')?'active in':''?>" id="default-tab-list">
            <?= $this->render('private-customer-list',[
                'title'=>'我的客户',
                'privateCustomerList' => isset($privateCustomerList)?$privateCustomerList:(new PrivateCustomer())->allList(),
                'model'=>$model,
                'ser_filter'=>isset($ser_filter)?$ser_filter:'',
            ])?>
        </div>
        <div class="tab-pane fade <?= (isset($tab) && $tab === 'add-customer')?'active in':''?>" id="default-tab-add">
            <?= $this->render('add',[
                'model'=>$model,
                'contactModel'=>isset($contactModel)?$contactModel:new ContactForm(),
            ])?>
        </div>
        <div class="tab-pane fade <?= (isset($tab) && $tab === 'touch-record-list')?'active in':''?>" id="default-touch-record-list">
            <?= $this->render('/customer-touch-record/customer-touch-record-list', [
                'touchRecordList'=>isset($touchRecordList)?$touchRecordList:(new CustomerTouchRecordMap())->allTouchRecord(),
                'ser_filter'=>isset($touch_record_list_ser_filter)?$touch_record_list_ser_filter:'',
            ])?>
        </div>
        <div class="tab-pane fade <?= (isset($tab) && $tab === 'contract-list')?'active in':''?>" id="default-contract-list">
            <?= $this->render('/customer-contract/list', [
                'operator'=>true,
                'contractList'=>isset($contractList)?$contractList:(new CustomerContractMap())->myContractList(),
                'ser_filter'=>isset($contract_list_ser_filter)?$contract_list_ser_filter:'',
                'need_filter'=>true,
                'back_url'=>\yii\helpers\Url::to([
                    '/crm/private-customer/index',
                    'tab'=>'contract-list',
                ]),
            ])?>
        </div>
    </div>
</div>