<?php
use backend\assets\AppAsset;
use yii\helpers\Html;
AppAsset::register($this);
?>
<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="zh-cn">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <?= Html::csrfMetaTags() ?>
        <title>calculate</title>
        <link href="<?= Yii::getAlias('@web')?>/css/calculate.css" rel="stylesheet" />
        <script src="<?= Yii::getAlias('@web')?>/js/calculate.js"></script>
    </head>
    <body onload="pageLoad()">
    <?php $this->beginBody() ?>
    <?= $content ?>
    <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage() ?>