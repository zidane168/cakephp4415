<?php
    use Cake\Core\Configure;
?>

<?php /// echo $this->Html->docType('html5'); cake 2?> 
<!DOCTYPE html>
<html>
	<head>
		<?php echo $this->Html->charset(); ?>

		<title>
			<?php echo Configure::read('site.name'); ?>
		</title>
        
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    
		<meta name="keywords" content="<?php echo Configure::read('site.keywords'); ?>">
        <meta name="description" content="<?php echo  Configure::read('site.description'); ?>">

        <?php 
    		echo $this->Html->meta('icon');

			echo $this->Html->meta('csrfToken', $this->request->getAttribute('csrfToken'));
			
			echo $this->Html->meta(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no']);
			echo $this->fetch('meta');

			echo $this->Html->css('newadminlte/bootstrap4.4.1.css');
			echo $this->Html->css('bootstrap-select/bootstrap-select.min.css');
			
			echo $this->Html->css('bs4-datetimepicker/bootstrap-datetimepicker.min.css');

			echo $this->Html->css('frontend.css?v=' . date('U'));
            echo $this->Html->css('fancy-box-v2.1.7/jquery.fancybox.css');
			
			echo $this->Html->script('plugins/jquery/jquery.min.js');
            echo $this->fetch('css');
		?>

		<script type="text/javascript" charset="utf-8">
			var cakephp = {
				// base_url: "<?=  $this->Url->build(['action' => 'index']);  ?>",
			}
		</script>
	</head>
    <body>
		<?php // echo $this->element('admin/menu/top_menu'); ?>
        <?php // echo $this->element('admin/menu/left_sidebar');  ?>
        <?= $this->fetch('content');  ?>		
	</body>
</html>
<?php //echo $this->element('sidebar_select'); ?>
