
<div class="container-fluid">

<div class="row">
	<div class="col-12">
		<div class="card card-primary">
			<div class="card-header">
				<h3 class="card-title"> <?=__d('administrator', 'edit_password'); ?> </h3>
			</div>

			<div class="card-body table-responsive">
				<?= $this->Form->create($administrator) ?>
				<fieldset>
					<?php
						echo "<br>";
						echo $this->Form->control('oldPassword', [
							'type' => 'password',
							'required' => 'true',
							'escape' => false,
							'label' => '<font class="red"> * </font>' .  __d('administrator', 'old_password'),
							'class' => 'form-control']);   
												   
						echo "<br>";
						echo $this->Form->control('newPassword', [
							'type' => 'password',
							'required' => 'true',
							'escape' => false,
							'id'	=> 'newPassword',
							'label' => '<font class="red"> * </font>' .  __d('administrator', 'new_password'),
							'class' => 'form-control']);  

						echo "<br>";
						echo $this->Form->control('confirmNewpassword', [
							'type' => 'password',
							'required' => 'true',
							'escape' => false,
							'onkeyup' => "onCheck()",
							'id'	=> 'confirmNewpassword',
							'label' => '<font class="red"> * </font>' .  __d('administrator', 'confirm_Newpassword'),
							'class' => 'form-control']);  
					?>

					<div class="row mt-10">
						<div class="col-2">
							<?= $this->Form->button(__('submit'), ['class' => 'btn btn-large btn-primary form-control']) ?>
						</div>
					</div>
			</fieldset>
			<?= $this->Form->end() ?>
		</div>
	</div>
</div>
</div>

<script>

	function onCheck() {
		var newPassword = $("#newPassword").val();
		var confirmNewpassword = $("#confirmNewpassword").val();
	
		$("#message").text("");
		$(":submit").removeAttr('disabled');

		if (newPassword != confirmNewpassword)  {
			$("#message").text("新的密碼和確定密碼不相同數字");
			$(":submit").attr('disabled', true);
		}	
	}
	
</script>