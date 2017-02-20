<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;
use yii\helpers\Url;
?>
<div class="site-error">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="alert alert-danger">
        <?= nl2br(Html::encode($errorMessage)) ?>
    </div>
    <a href="<?= Url::to([$backUrl])?>"><button type="button" class="btn btn-default btn-large">返回</button></a>
</div>
