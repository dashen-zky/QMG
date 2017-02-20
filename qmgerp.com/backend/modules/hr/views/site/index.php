<?php
use yii\helpers\Url;
?>

<?php

/* @var $this yii\web\View */
$this->title = 'hr resource';
?>
<div class="site-index">
    <div class="body-content">
        <p><a class="btn btn-lg btn-success" href="<?php echo Url::to(['department/index'])?>">show department</a></p>
        <p><a class="btn btn-lg btn-success" href="<?php echo Url::to(['department/add'])?>">add department</a></p>
        <p><a class="btn btn-lg btn-success" href="<?php echo Url::to(['department/edit'])?>">edit department</a></p>

        <p><a class="btn btn-lg btn-success" href="<?php echo Url::to(['position/index'])?>">show position</a></p>
        <p><a class="btn btn-lg btn-success" href="<?php echo Url::to(['position/add'])?>">add position</a></p>
        <p><a class="btn btn-lg btn-success" href="<?php echo Url::to(['position/edit'])?>">edit position</a></p>

        <p><a class="btn btn-lg btn-success" href="<?php echo Url::to(['employee/index'])?>">show employee</a></p>
        <p><a class="btn btn-lg btn-success" href="<?php echo Url::to(['employee/add'])?>">add employee</a></p>
        <p><a class="btn btn-lg btn-success" href="<?php echo Url::to(['employee/edit'])?>">edit employee</a></p>
    </div>
</div>
