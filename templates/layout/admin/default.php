<?php

/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

use Cake\Core\Configure;
?>


<?php /// echo $this->Html->docType('html5'); cake 2
?>
<!DOCTYPE html>
<html>

<head>
	<?php echo $this->Html->charset(); ?>

	<title>
		<?php echo Configure::read('site.name'); ?>
	</title>

	<style>
		html {
			scroll-behavior: smooth;
		}
	</style>


	<!-- <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback"> -->


	<meta name="keywords" content="<?php echo Configure::read('site.keywords'); ?>">

	<meta name="description" content="<?php echo  Configure::read('site.description'); ?>">

	<?php 
	echo $this->Html->meta('logo.png', 'favicon.ico', array('type' => 'icon'));
	echo $this->Html->meta('csrfToken', $this->request->getAttribute('csrfToken'));

	echo $this->Html->meta(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no']);
	echo $this->fetch('meta');

	echo $this->Html->css('roboto-font.css');  

	echo $this->Html->css('newadminlte/bootstrap4.4.1.css');
	echo $this->Html->css('bootstrap-select/bootstrap-select.min.css');

	echo $this->Html->css('bs4-datetimepicker/bootstrap-datetimepicker.min.css');

	echo $this->Html->css('newadminlte/all.min.css');
	echo $this->Html->css('newadminlte/adminlte.css?v=' . date('U'));

	echo $this->Html->css('cms.css?v=' . date('U'));

	echo $this->Html->css('fancy-box-v2.1.7/jquery.fancybox.css');

	// overlayScrollbars
	echo $this->Html->css('plugins/overlayscrollbars/overlayscrollbars.min.css');

	echo $this->Html->script('plugins/jquery/jquery.min.js');

	// sortable
	echo $this->Html->script('plugins/jquery-ui/jquery-ui.min.js');

	echo $this->fetch('css');
	?>
 
</head>

<body class="hold-transition sidebar-mini layout-fixed">
	<?php echo $this->element('admin/menu/top_menu'); ?>
	<?php echo $this->element('admin/menu/left_sidebar');  ?>

	<div class="content-wrapper">

		<!-- Content Header (Page header) -->
		<section class="content-header">
			<div class="container-fluid">

				<div class="mb-2 row">
					<div class="col-sm-6">
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<!-- <li class="breadcrumb-item">
								<a href="#"> <?= ($this->request->getParam('action')); ?> </a>
							</li> -->
							<li class="breadcrumb-item active"><?= ($this->request->getParam('action')); ?></li>
						</ol>
					</div>
				</div>
			</div>
			<div class="content-booster">
				<?= $this->Flash->render();  
				?>
				<?= $this->fetch('content');  ?>

				<div>
					<?php echo $this->element('admin/menu/footer'); ?>
				</div>
			</div>
		</section>

		<!-- 6.1 --> 
		<?php
		echo $this->Html->script('newadminlte/popper1.16.min.js');
		echo $this->Html->script('newadminlte/bootstrap4.4.1.min.js');
		echo $this->Html->script('CakeAdminLTE/moment.min');
		// echo $this->Html->script('plugins/datetimepicker/bootstrap-datetimepicker.min');
		echo $this->Html->script('plugins/bootstrap-select/bootstrap-select.min.js');

		echo $this->Html->script('plugins/bs4-datetimepicker/bootstrap-datetimepicker.min.js');

		echo $this->Html->script('plugins/flot/jquery.flot.js');
		// overlayScrollbars 
		echo $this->Html->script('plugins/overlayscrollbars/jquery.overlayscrollbars.min.js');
		echo $this->Html->script('newadminlte/adminlte.js');
		echo $this->Html->script('ckeditor_basic/ckeditor');
		echo $this->Html->script('CakeAdminLTE/common.js?v=' . date('U'));

		echo $this->Html->script('fancy-box-v2.1.7/jquery.fancybox.pack.js');
		echo $this->Html->script('CakeAdminLTE/bootbox.min.js');

		echo $this->Html->script('jquery.validate.min.js');

		$session = $this->request->getSession();
		$language = $session->read('Config.language'); 

		echo $this->Html->script('locale/lang.'. $language . '.js?v=' . date('U'));

		echo $this->fetch('scriptBottom');
		echo $this->fetch('script');
		?>
		<script type="text/javascript">
			$('.btn-change-language').on('click', function() {
				$('#form-language').submit();
			});
		</script>

		<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
		<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

	</div>
</body>

</html>



<!-- 

I've created this tool: https://lingtalfi.com/bootstrap4-color-generator, you simply put primary in the first field, then choose your color, and click generate.

Then copy the generated scss or css code, and paste it in a file named my-colors.scss or my-colors.css (or whatever name you want).

Once you compile the scss into css, you can include that css file AFTER the bootstrap CSS and you'll be good to go.

The whole process takes about 10 seconds if you get the gist of it, provided that the my-colors.scss file is already created and included in your head tag.

Note: this tool can be used to override bootstrap's default colors (primary, secondary, danger, ...), but you can also create custom colors if you want (blue, green, ternary, ...).

Note2: this tool was made to work with bootstrap 4 (i.e. not any subsequent version for now). -->