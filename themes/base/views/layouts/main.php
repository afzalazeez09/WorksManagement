<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<!-- Nb: Andrew Blake shifted this line from bottom to here as although meant to be faster to load scripts at bottom
		nested set admin gui extension needs it up here for some reason -->
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
	<?php
		// Suppress Yii's autoload of JQuery
		// We're loading it at bottom of page (best practice)
		// from Google's CDN with fallback to local version 
		Yii::app()->clientScript->scriptMap=array(
			'jquery.js'=>false,
			'jquery.min.js'=>false,
		);
		
		// Load Yii's generated javascript at bottom of page
		// instead of the 'head', ensuring it loads after JQuery
		Yii::app()->getClientScript()->coreScriptPosition = CClientScript::POS_END;
	?>
	
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
	<meta name="description" content="">
	<meta name="author" content="">

	<meta name="viewport" content="width=device-width">
	
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/bootstrap-and-responsive.min.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/style.css" />
	<script src="<?php echo Yii::app()->theme->baseUrl; ?>/js/libs/modernizr-2.5.3-respond-1.1.0.min.js"></script>
</head>
<body>

<div class="container">
	<div class="row">
		<header class="span12">

			<?php $this->widget('bootstrap.widgets.TbNavbar', array(
				'fixed'=>false,
				'brand'=>Yii::app()->name,
				'brandUrl'=>'#',
				'collapse'=>true, // requires bootstrap-responsive.css
				'items'=>array(
 					$this->reportsMenu,
					//$this->operations,
					array(
						'class'=>'bootstrap.widgets.TbMenu',
						'items'=>array(
							Yii::app()->user->checkAccess('system admin')
								? array('label'=>'Database', 'url'=>Yii::app()->request->hostInfo.'/phpMyAdmin')
								: array(),
						),
					),
					array(
						'class'=>'bootstrap.widgets.TbMenu',
						'htmlOptions'=>array('class'=>'pull-right'),
						'items'=>array(
							array('label'=>'Login', 'url'=>array('/site/login'), 'visible'=>Yii::app()->user->isGuest),
							array('label'=>'Logout ('.Yii::app()->user->name.')', 'url'=>array('/site/logout'), 'visible'=>!Yii::app()->user->isGuest),
						),
					),
				),
			)); ?>
			
			<!-- breadcrumbs -->
			<?php if(isset($this->breadcrumbs)):?>
				<?php $this->widget('bootstrap.widgets.TbBreadcrumbs', array(
					'links'=>$this->breadcrumbs,
				)); ?>
			<?php endif?>


			<!-- tabs -->
			<?php
			if($t = $this->tabs)
			{
				$this->widget('bootstrap.widgets.TbMenu', array(
					'type'=>'tabs', // '', 'tabs', 'pills' (or 'list')
					'stacked'=>false, // whether this is a stacked menu
					'items'=>$this->tabs,
				));
			}
			?>
				
			<?php
			if(isset($this->heading) && $this->heading)
			{
				echo '<h2>'.CHtml::encode($this->heading);
				// if we should be showing the new button
				if(Yii::app()->controller->action->id == 'admin')
				{
					echo ' ';
//					echo CHtml::submitButton('Download Excel');
					$this->widget('bootstrap.widgets.TbButton', array(
						'label'=>'Download Excel',
						'url'=>$this->createUrl("{$this->modelName}/admin", array('action'=>'download')),
						'type'=>'primary',
						'size'=>'small', // '', 'large', 'small' or 'mini'
//						'htmlOptions'=>array('data-toggle'=>'modal'),
					));
					echo ' ';
					$this->widget('bootstrap.widgets.TbButton', array(
						'label'=>'New',
						'url'=>'#myModal',
						'type'=>'primary',
						'size'=>'small', // '', 'large', 'small' or 'mini'
						'htmlOptions'=>array(
							'data-toggle'=>'modal',
// TODO: alter this slightly to focus on first element when combined with others ie. textarea
							'onclick' => '$("#myModal input:not([class="hasDatepicker"]):visible:enabled:first").focus();',
						),
					));
				}
				echo '</h2>';
			}
			?>
		</header>
	</div>
	
	<div class="row">
		<div class="span12">
			<?php echo $content; ?>
		</div>
	</div>

</div><!-- container -->
<script>window.jQuery || document.write('<script src="<?php echo Yii::app()->theme->baseUrl; ?>/js/libs/jquery-1.7.2.min.js"><\/script>')</script>
<!-- container -->
<script src="<?php echo Yii::app()->theme->baseUrl; ?>/js/libs/bootstrap/bootstrap.min.js"></script>

<script src="<?php echo Yii::app()->theme->baseUrl; ?>/js/script.js"></script>
</body>
</html>