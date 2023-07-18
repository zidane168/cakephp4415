<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Role $role
 */
?>
<button class="btn-scroll-to-top" type="button" onclick="scrollWin()" id="myBtn">Top</button>
<div class="container-fluid">

	<div class="row">

		<div class="col-12">
			<div class="card card-primary">
				<div class="card-header">
					<h3 class="card-title"> <?= __d('role', 'edit_role'); ?> </h3>
				</div>

				<div class="card-body table-responsive">

					<?= $this->Form->create($role) ?>
					<fieldset>
						<legend><?= __d('role', 'edit_role'); ?></legend>
						<?php
						echo $this->Form->control('slug', ['class' => 'form-control', 'label' => __('slug')]);
						echo $this->Form->control('name', ['class' => 'form-control', 'label' => __('name')]);
						?>

						<div class="row mt-15">
							<div class="col-md-12">
							<div class="float-left"> 
								<?= $this->Form->button(__('submit'), [
									'style' => 'padding: 0 50px',
									'class' => 'btn btn-large btn-warning form-control']) ?>
									
							</div>
								<div class="float-right">
									<button type="button" class="btn btn-box-tool btn-primary" id="collapse">
										<i class="text-white fa fa-minus"></i> <?php echo __('collapse_all')  ?>
									</button>
									<button type="button" class="btn btn-box-tool btn-success" id="expand">
										<i class="text-white fa fa-plus" ></i> <?php echo __('expand_all')  ?>
									</button>
								</div>
							</div>
						</div>

						<?php if (isset($permissions_matrix) && !empty($permissions_matrix)) { ?>
							<fieldset style="margin-top: 15px">
								<h4> <?= __d('role', 'permissions_management') ?> </h4>
								<div class="row">
									<?php $flag_row = 0 ?>
									<?php foreach ($permissions_matrix as $key => $matrix) { ?>
										<?php
										$is_checked = false;
										foreach ($matrix as $rule) {
											if (in_array($rule['id'], $current_permissions)) {
												$is_checked = true;
												break;
											}
										}
										?>
										<div class="col-xs-12 col-sm-12 col-md-6 my-box">
											<div class="card card-primary box <?= $is_checked ? 'collapsed-box' : '' ?>">
												<div class="card-header with-border">
													<h3 class="card-title">
													<?php 
														echo $this->element('permission_translate', ['key' => $key]);
													?>
													</h3>
													<div class="float-right box-tools">
														<button type="button" class="btn btn-box-tool" data-widget="collapse">
															<i class="text-white fa <?= $is_checked ? "fa-minus" : "fa-plus" ?>"></i>
														</button>
														<!-- <button type="button" class="btn btn-box-tool" data-widget="remove">
															<i class="fa fa-times"></i>
														</button> -->
													</div>
												</div>
												<!-- <div class="box-body tbl-role-permission"> -->
												<div class="card-body">
													<table class="table table-bordered">
														<thead>
															<tr>
																<th class="text-center">#</th>
																<th class="text-center"><?php echo __d('role', 'rule'); ?></th>
																<th class="text-center" ><?php echo __d('role', 'action'); ?></th>
																<th class="text-center"><?php
																						echo $this->Form->input('chk_all', array(
																							'type' => 'checkbox',
																							'label' => false,
																							'hiddenField' => false,
																							'class' => 'chk-all-permission'
																						));
																						?></th>
															</tr>
														</thead>

														<tbody>
															<?php foreach ($matrix as $key => $rule) {  ?>
																<tr>
																	<td class="text-center"><?php echo $rule['id']; ?></td>
																	<td><?php echo $rule['name']; ?></td>
																	<td class="text-center"><?= __($rule['action']) ?></td>
																	<td class="text-center"><?php
																							echo $this->Form->control('rules[]', array(
																								'type' => 'checkbox',
																								'label' => false,
																								'hiddenField' => false,
																								'class' => 'chk-permission-id',
																								'value' => $rule['id'],
																								'checked' => in_array($rule['id'], $current_permissions)
																							));
																							?></td>
																</tr>
															<?php } ?>
														</tbody>
													</table>
												</div>
											</div>
										</div>
										<?php
										$flag_row++;
										if ($flag_row % 2 == 0) {
											echo '</div><div class="row">';
										}
										?>
									<?php } ?>
								</div>
							</fieldset>
						<?php } ?>

						<div class="mt-10 row">
							<div class="col-2">
								<?= $this->Form->button(__('Submit'), [
									'style' => 'padding: 0 50px',
									'class' => 'btn btn-large btn-warning form-control']) ?>
							</div>

						</div>
					</fieldset>
					<?= $this->Form->end() ?>
				</div>
			</div>
		</div>
	</div>
</div>

<?= $this->Html->script('CakeAdminLTE/pages/admin_role.js?v=' . date('U'), array('inline' => false)); ?>
