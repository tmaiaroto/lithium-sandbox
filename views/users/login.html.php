<div class="container_16 no-bg">
	<div class="grid_10">
		<div class="box-bg">
			<div class="box-content">
				<h1>Login with your account</h1>
				<?=$this->form->create(null, array('id' => 'login-form')); ?>
				<fieldset class="login">
					<div class="input"><?=$this->form->field('email'); ?></div>
					<div class="input"><?=$this->form->field('password', array('type' => 'password')); ?></div>
					<?=$this->form->submit('Log in', array('class' => 'submit')); ?>
				</fieldset>
				<?=$this->form->end(); ?>
			</div>
		</div>
	</div>
</div>