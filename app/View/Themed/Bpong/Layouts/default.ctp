<!DOCTYPE html>
<html lang="en">
	<head>
		<!-- Meta -->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta http-equiv="pragma" content="no-cache" />
		<?php
		echo $this->fetch('meta');
		?>
		<title><?php echo $this->fetch('title'); ?></title>

		<link rel='stylesheet' type='text/css' href='//fonts.googleapis.com/css?family=Open+Sans:400,300,600&amp;subset=cyrillic,latin'>


		<?php
		// CSS Global Compulsory
		echo $this->Html->css('bootstrap.min');
		echo $this->Html->css('style');

		// CSS Header and Footer
		echo $this->Html->css('header-v6');
		echo $this->Html->css('footer-v1');

		// CSS Theme
		echo $this->Html->css('theme-skins/default');

		// CSS Plugins
		echo $this->Html->css('line-icons/line-icons');
		echo $this->Html->css('font-awesome/css/font-awesome.min.css');

		// CSS Custom
		echo $this->Html->css('custom');
		?>

		<?php
			echo $this->fetch('css');
		?>
	</head>
	<body class="header-fixed header-fixed-space">
		<div class="wrapper">
			<?php echo $this->element('header'); ?>
			<!--=== Content ===-->
			<div class="container content">
				<?php echo $this->fetch('content'); ?>
			</div>
			<!--=== End Content ===-->
			<?php echo $this->element('footer'); ?>
		</div>
		<!-- JS Global Compulsory -->
		<?php
			// JS Global Compulsory
			echo $this->Html->script('jquery/jquery.min');
			echo $this->Html->script('jquery/jquery-migrate.min');
			echo $this->Html->script('bootstrap/bootstrap.min');

			// JS Plugins
			echo $this->Html->script('back-to-top');
			echo $this->Html->script('smoothScroll');
			echo $this->Html->script('jquery.parallax');

			// JS Customization
			echo $this->Html->script('custom');
			echo $this->Html->script('app');
		?>
		<script type="text/javascript">
			jQuery(document).ready(function() {
				App.init();
			});
		</script>
		<!--[if lt IE 9]>
		<?php
			echo $this->Html->script('respond');
			echo $this->Html->script('html5shiv');
			echo $this->Html->script('placeholder-IE-fixes');
		?>
		<![endif]-->
		<?php echo $this->fetch('script'); ?>
</body>
</html>
