<?php 
use Cake\Routing\Router;
?>
<div id="login">
 	<div id="logo">
 		<?php
			echo $this->Html->image('cidckids/logo2.png', array(
				"style" => 'width: 300px; ',
				"alt"   => 'Logo',
			));  
		?>
 	</div>

 	<div id="main">  
		<div id="login-form">

			<?= $this->Form->create($administrator) ?> 
				<div class="row">
					<div class="has-feedback col-12">
						<?php
							echo $this->Form->control('email', array(
								'class' => 'form-control',
								'placeholder' => __('email'), 
								'label' => __('email') . "<font class='red'> * </font>",
								'escape' => false
							));
							?>

					</div>
				</div>

				<div class="row" style="margin-top: 10px">
					<div class="group-password col-12">
						<?php
							echo $this->Form->control('password', array(
								'class' => 'form-control',
								'placeholder' => __('password'),
								'id' => 'password', 
								'escape' => false,
								'label' =>  __('password') . '<font class="red"> * </font>',
							));
						?>   

						<img class="eye eye-open" src="<?= Router::url('/', true)?>/webroot/img/cidckids/icon/eye-close.svg" alt="eye-open" />
						<!-- <img class="hidden eye eye-close" src="<?= Router::url('/', true)?>/webroot/img/cidckids/icon/eye-close.svg" alt="eye-open" /> -->
					</div>
				</div> 

				<div class="row button-space">
					<div class="w-full col-12">
						<?php
							echo $this->Form->submit(__('sign_in_now'), array(
								'class' => 'btn button-color btn-block btn-flat color-white'
							));
							?>
					</div>
				</div> 
			<?= $this->Form->end() ?>
		</div> 
 	</div>  
</div>

<script>
    $('document').ready(function() {
		$('.eye').on('click', function() {   
			const password = $("#password");
			let type  = "password";
			if (password.attr('type') == 'password') {
				$('img.eye').attr("src", '<?= Router::url('/', true)?>/webroot/img/cidckids/icon/eye-open.svg');
				type  = "text";

			} else {
				$('img.eye').attr("src", '<?= Router::url('/', true)?>/webroot/img/cidckids/icon/eye-close.svg');
				type  = "password";
			} 
			
			password.attr("type", type);
		});
    })
</script>
 