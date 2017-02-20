<?php
use yii\widgets\Pjax;
use backend\models\MyLinkPage;
use yii\web\View;
if(!isset($model)) {
    $model = new \backend\modules\crm\models\touchrecord\TouchRecordForm();
    $model->setConfig((new \backend\modules\crm\models\customer\model\CustomerConfig())->generateConfig());
}
?>
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
<?= $this->render('list-filter-form',[
    'formData'=>unserialize(isset($ser_filter)?$ser_filter:''),
]);?>
    <table class="table table-striped">
        <thead>
        <tr>
            <th class="col-md-2">项目</th>
            <th>跟进人</th>
            <th>跟进时间</th>
            <th>跟进方式</th>
            <th>联系人</th>
            <th>结果</th>
            <th>预计签约时间</th>
            <th>预计签约金额</th>
            <th>下次跟进时间</th>
            <th class="col-md-2">跟进情况</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $touchResultList = $model->getList('touchResult');
        $touchTypeList = $model->getList('touchType');
        ?>
        <?php foreach ($touchRecordList['touchRecordList'] as $item) :?>
            <tr>
                <td><?= $item['project_name']?></td>
                <td><?= $item['follow_name']?></td>
                <td><?= ($item['time'] != 0)?date("Y-m-d",$item['time']):''?></td>
                <td><?= isset($touchTypeList[$item['type']])?$touchTypeList[$item['type']]:''?></td>
                <td><?= $item['contact_name']?></td>
                <td><?= isset($touchResultList[$item['result']])?$touchResultList[$item['result']]:''?></td>
                <td><?= ($item['predict_contract_time'] != 0)?date("Y-m-d",$item['predict_contract_time']):''?></td>
                <td><?= $item['predict_contract_value']?></td>
                <td><?= ($item['next_touch_time'] != 0)?date("Y-m-d",$item['next_touch_time']):''?></td>
                <td><?= $item['description']?></td>
            </tr>
        <?php endforeach?>
        </tbody>
    </table>
<?php
if(isset($ser_filter) && !empty($ser_filter)) {
    $pageParams = [
        'pagination' => $touchRecordList['pagination'],
        'ser_filter'=>$ser_filter,
    ];
} else {
    $pageParams = [
        'pagination' => $touchRecordList['pagination'],
    ];
}
?>
<?= MyLinkPage::widget($pageParams); ?>
<?php Pjax::end(); ?>