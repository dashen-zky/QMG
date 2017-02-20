<?php
use yii\widgets\Pjax;
use yii\widgets\LinkPager;
use yii\helpers\Url;
use backend\models\ListIndex;
use backend\modules\fin\models\account\models\FINAccountForm;
?>
<!-- begin panel -->
<div class="panel panel-body fin-account-list">
<table class="table table-striped">
    <thead>
    <tr>
        <th>名字</th>
        <th>开户行</th>
        <th>账号</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($receiveCompanyList as $item) :?>
        <tr>
            <td><?= $item['name']?></td>
            <td><?= $item['bank_of_deposit']?></td>
            <td><?= $item['account']?></td>
            <td>
                <div>
                    <a href="<?= Url::to([
                        '/accountReceivable/receive-company/del',
                        'uuid'=>$item['uuid'],
                    ])?>">删除</a>
                </div>
            </td>
        </tr>
    <?php endforeach?>
    </tbody>
</table>
</div>
<!-- end panel -->