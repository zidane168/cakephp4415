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
<!DOCTYPE html>
<html>

<head>
	<?php echo $this->Html->charset(); ?>

	<title>
		<?php echo Configure::read('site.name'); ?>
		<?php echo isset($title_for_layout) && !empty($title_for_layout) ? $title_for_layout : ''; ?>
	</title>

	<meta name="keywords" content="<?php echo Configure::read('site.keywords'); ?>">
	<meta name="description" content="<?php echo Configure::read('site.description'); ?>">

	<?php
	echo $this->Html->meta('logo.png', 'img/favicon.ico', array('type' => 'icon'));
	echo $this->Html->meta(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no']);
	echo $this->fetch('meta');

	echo $this->Html->css('newadminlte/bootstrap4.4.1.css');

	echo $this->fetch('css');

	/**
	 * Custom css
	 */

	// ----------- COLOR TEMPLATE -------------- 
	echo $this->Html->css('login.css?v=' . date('U'));
	// ----------- COLOR TEMPLATE --------------

	echo $this->fetch('css');
	echo $this->Html->script('plugins/jquery/jquery.min.js');
	echo $this->Html->script('newadminlte/bootstrap4.4.1.min.js');

	echo $this->fetch('scriptTop');
	?>

</head>

<body class="skin-blue">
	<div class="wrapper row-offcanvas row-offcanvas-left">
		<section class="">
			<?= $this->Flash->render(); // $this->Session->flash(); 
			?>

			<?php echo $this->fetch('content'); ?>
		</section> 
 
	</div><!-- ./wrapper -->
</body>

</html>