<?php
use yii\widgets\LinkPager;
$statusList = \backend\modules\crm\models\customer\model\ContactForm::enableList();
?>

<table class="table table-striped">
    <thead>
    <tr>
        <th>选择</th>
        <th>名称</th>
        <th>职位</th>
        <th>性别</th>
        <th>电话</th>
        <th>微信</th>
        <th>qq</th>
        <th>邮箱</th>
        <th>地址</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($contactList['contactList'] as $item) :?>
        <tr>
            <td>
                <input type="checkbox" name="uuid" value="<?= $item['uuid']?>"
                    <?= in_array($item['uuid'], $uuids)?'checked':''?>>
            </td>
            <td class="contactName"><?= $item['name']?></td>
            <td><?= $item['position']?>/
                <?= isset($statusList[$item['enable']])?$statusList[$item['enable']]:null?></td>
            <td><?= $item['gender']?></td>
            <td><?= $item['phone']?></td>
            <td><?= $item['weichat']?></td>
            <td><?= $item['qq']?></td>
            <td><?= $item['email']?></td>
            <td><?= $item['address']?></td>
        </tr>
    <?php endforeach?>
    </tbody>
</table>
<?= LinkPager::widget(['pagination' => $contactList['pagination']]); ?>
<a href="#" class="<?= $selectClass?>"><button class="btn btn-primary col-md-3" style="float: right">选择</button></a>
