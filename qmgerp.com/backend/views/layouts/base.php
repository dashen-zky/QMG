<?php
use yii\helpers\Url;
use backend\assets\AppAsset;
use yii\helpers\Html;

$appAsset = AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
	<meta charset="utf-8" />
	<title>谦玛 ERP</title>
	<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />
	<meta content="" name="description" />
	<meta content="" name="author" />

	<!-- ================== BEGIN BASE CSS STYLE ================== -->
	<link href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
	<link href="<?= Yii::getAlias('@web')?>/plugins/jquery-ui/themes/base/minified/jquery-ui.min.css" rel="stylesheet" />
	<link href="<?= Yii::getAlias('@web')?>/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
	<link href="<?= Yii::getAlias('@web')?>/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
	<link href="<?= Yii::getAlias('@web')?>/css/animate.min.css" rel="stylesheet" />
	<link href="<?= Yii::getAlias('@web')?>/css/style.min.css" rel="stylesheet" />
	<link href="<?= Yii::getAlias('@web')?>/css/style-responsive.min.css" rel="stylesheet" />
	<link href="<?= Yii::getAlias('@web')?>/css/theme/default.css" rel="stylesheet" id="theme" />
	<link href="<?= Yii::getAlias('@web')?>/css/jquery.datetimepicker.css" rel="stylesheet"/>
	<link href="<?= Yii::getAlias('@web')?>/css/lq.datetimepick.css" rel="stylesheet"/>
	<!-- ================== END BASE CSS STYLE ================== -->
	<!-- ================== BEGIN BASE CSS STYLE ================== -->
	<link href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
	<link href="<?= Yii::getAlias('@web')?>/plugins/jquery-ui/themes/base/minified/jquery-ui.min.css" rel="stylesheet" />
	<link href="<?= Yii::getAlias('@web')?>/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
	<link href="<?= Yii::getAlias('@web')?>/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
	<link href="<?= Yii::getAlias('@web')?>/css/animate.min.css" rel="stylesheet" />
	<link href="<?= Yii::getAlias('@web')?>/css/style.min.css" rel="stylesheet" />
	<link href="<?= Yii::getAlias('@web')?>/css/style-responsive.min.css" rel="stylesheet" />
	<link href="<?= Yii::getAlias('@web')?>/css/theme/default.css" rel="stylesheet" id="theme" />
	<!-- ================== END BASE CSS STYLE ================== -->
	<?= Html::csrfMetaTags() ?>
	<!-- ================== BEGIN BASE JS ================== -->
	<script src="<?= Yii::getAlias('@web')?>/plugins/jquery/jquery-1.9.1.min.js"></script>
	<script src="<?= Yii::getAlias('@web')?>/plugins/pace/pace.min.js"></script>
	<script src="<?= Yii::getAlias('@web')?>/js/jquery.datetimepicker.js"></script>
	<script src="<?= Yii::getAlias('@web')?>/js/datetime.js"></script>
	<!-- ================== END BASE JS ================== -->
	<!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
	<link href="<?= Yii::getAlias('@web')?>/plugins/parsley/src/parsley.css" rel="stylesheet" />
	<link href="<?= Yii::getAlias('@web')?>/plugins/jstree/dist/themes/default/style.min.css" rel="stylesheet" />
	<!-- ================== END PAGE LEVEL STYLE ================== -->
	<!---------uEditor------------------------------------------->
	<script type="text/javascript" charset="utf-8" src="<?= Yii::getAlias('@web')?>/ueditor/ueditor.config.js"></script>
	<script type="text/javascript" charset="utf-8" src="<?= Yii::getAlias('@web')?>/ueditor/ueditor.all.js"></script>
	<!--建议手动加在语言，避免在ie下有时因为加载语言失败导致编辑器加载失败-->
	<!--这里加载的语言文件会覆盖你在配置项目里添加的语言类型，比如你在配置项目里配置的是英文，这里加载的中文，那最后就是中文-->
	<script type="text/javascript" charset="utf-8" src="<?= Yii::getAlias('@web')?>/ueditor/lang/zh-cn/zh-cn.js"></script>
	<!----------------uEditor-------------------------------------------------------->
	<?php $this->head()?>
	<?= Html::csrfMetaTags() ?>
</head>
<body>
	<?php $this->beginBody()?>
	<!-- begin #page-loader -->
	<div id="page-loader" class="fade in"><span class="spinner"></span></div>
	<!-- end #page-loader -->
	
	<?= $content?>
	
	<!-- ================== BEGIN BASE JS ================== -->
<!--	<script src="--><?//= Yii::getAlias('@web')?><!--/plugins/jquery/jquery-1.9.1.min.js"></script>-->
	<script src="<?= Yii::getAlias('@web')?>/plugins/jquery/jquery-migrate-1.1.0.min.js"></script>
	<script src="<?= Yii::getAlias('@web')?>/plugins/jquery-ui/ui/minified/jquery-ui.min.js"></script>
	<script src="<?= Yii::getAlias('@web')?>/plugins/bootstrap/js/bootstrap.min.js"></script>
	<!--[if lt IE 9]>
		<script src="<?= Yii::getAlias('@web')?>/crossbrowserjs/html5shiv.js"></script>
		<script src="<?= Yii::getAlias('@web')?>/crossbrowserjs/respond.min.js"></script>
		<script src="<?= Yii::getAlias('@web')?>/crossbrowserjs/excanvas.min.js"></script>
	<![endif]-->
	<script src="<?= Yii::getAlias('@web')?>/plugins/slimscroll/jquery.slimscroll.min.js"></script>
	<script src="<?= Yii::getAlias('@web')?>/plugins/jquery-cookie/jquery.cookie.js"></script>
	<!-- ================== END BASE JS ================== -->
	
	<!-- ================== BEGIN PAGE LEVEL JS ================== -->
	<script src="<?= Yii::getAlias('@web')?>/js/inbox.demo.min.js"></script>
	<script src="<?= Yii::getAlias('@web')?>/js/apps.min.js"></script>
	<!-- ================== END PAGE LEVEL JS ================== -->
	<!-- ================== BEGIN PAGE LEVEL JS ================== -->
	<script src="<?= Yii::getAlias('@web')?>/plugins/jstree/dist/jstree.min.js"></script>
	<script src="<?= Yii::getAlias('@web')?>/js/ui-tree.demo.min.js"></script>
	<!-- ================== END PAGE LEVEL JS ================== -->
	<!-- ================== BEGIN PAGE LEVEL JS ================== -->
	<script src="<?= Yii::getAlias('@web')?>/plugins/parsley/dist/parsley.js"></script>
	<script src="<?= Yii::getAlias('@web')?>/js/apps.min.js"></script>
	<!-- ================== END PAGE LEVEL JS ================== -->
	<script src="<?= Yii::getAlias('@web')?>/js/common.js"></script>
	<!-----------------------------滚动条----------------------------------->
	<script src="<?= Yii::getAlias('@web')?>/js/zUI.js"></script>
	<!-----------------------------滚动条----------------------------------->
	<script>
		$(document).ready(function() {
			App.init();
			Inbox.init();
			TreeView.init();
//			FormPlugins.init();
			$(".scroll").panel({iWheelStep:32});
		});
	</script>
	<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage()?>
