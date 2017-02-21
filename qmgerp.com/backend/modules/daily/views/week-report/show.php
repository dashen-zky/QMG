<?php
use backend\modules\daily\models\transaction\TransactionConfig;
$config = new TransactionConfig();
?>
<div>
    <table class="table">
        <tr>
            <td>标题</td>
            <td>
                <input class="form-control" disabled value="<?= $formData['title']?>">
            </td>
            <td>创建日期</td>
            <td>
                <input disabled class="form-control"
                       value="<?= $formData['created_time'] != 0 ?date("Y-m-d",$formData['created_time']):null?>">

            </td>
            <td>创建人</td>
            <td>
                <input class="form-control" disabled value="<?= $formData['created_name']?>"></td>
        </tr>
        <tr>
            <td>本周内容</td>
            <td colspan="5">
                <textarea class="form-control" disabled rows="5">
                    <?= $formData['content']?>
                </textarea>

            </td>
        </tr>
        <tr>
            <td>下周安排</td>
            <td colspan="5">
                <textarea class="form-control" disabled rows="5">
                    <?= $formData['next_content']?>
                </textarea>

            </td>
        </tr>
    </table>
</div>
<?= $this->render('@webroot/../views/site/panel-header',[
    'title'=>'本周完成的事项',
])?>
<table class="table table-striped">
    <thead>
        <th class="col-md-1">标题</th>
        <th class="col-md-1">任务截止时间</th>
        <th class="col-md-1">完成时间</th>
        <th class="col-md-1">执行人</th>
        <th class="col-md-2">状态</th>
        <th class="col-md-6">内容</th>
    </thead>
    <tbody>
        <?php foreach ($finishedTransactionList as $item):?>
            <tr>
                <td><?= $item['title']?></a></td>
                <td><?= isset($item['expect_finish_time']) &&
                    $item['expect_finish_time'] != 0
                        ?date("Y-m-d",$item['expect_finish_time'])
                        :null?></td>
                <td><?= isset($item['finished_time']) &&
                    $item['finished_time'] != 0
                        ?date("Y-m-d",$item['finished_time'])
                        :null?></td>
                <td><?= $item['execute_name']?></td>
                <td><?= $config->getAppointed('status', $item['status'])?></td>
                <td><?= $item['content']?></td>
            </tr>
        <?php endforeach;?>
    </tbody>
</table>
<?= $this->render('@webroot/../views/site/panel-footer')?>

<?= $this->render('@webroot/../views/site/panel-header',[
    'title'=>'下周计划的事项',
])?>
<table class="table table-striped">
    <thead>
    <th class="col-md-1">标题</th>
    <th class="clo-md-1">任务截止时间</th>
    <th class="col-md-1">执行人</th>
    <th class="col-md-1">状态</th>
    <th class="col-md-7">内容</th>
    </thead>
    <tbody>
    <?php foreach ($unfinishedTransactionList as $item):?>
        <tr>
            <td><?= $item['title']?></a></td>
            <td><?= isset($item['expect_finish_time']) &&
                $item['expect_finish_time'] != 0
                    ?date("Y-m-d",$item['expect_finish_time'])
                    :null?></td>
            <td><?= $item['execute_name']?></td>
            <td><?= $config->getAppointed('status', $item['status'])?></td>
            <td><?= $item['content']?></td>
        </tr>
    <?php endforeach;?>
    </tbody>
</table>
<?= $this->render('@webroot/../views/site/panel-footer')?>
