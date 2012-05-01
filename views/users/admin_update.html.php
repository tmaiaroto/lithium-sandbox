<div class="grid_16">
	<h2 id="page-heading">Update User</h2>  
</div>
<div class="clear"></div>

<div class="grid_12">
	<?=$this->form->create($document, array('id' => 'user-update-form', 'onSubmit' => 'return submitCheck();')); ?>
		<?=$this->security->requestToken(); ?>
		<div class="input">
			<?=$this->form->label('User Role'); ?>
			<?=$this->form->select('role', $roles); ?>
		</div>
		<div class="input"><?=$this->form->field('first_name', array('label' => 'First Name', 'placeholder' => 'John'));?></div>
		<div class="input"><?=$this->form->field('last_name', array('label' => 'Last Name', 'placeholder' => 'Doe'));?></div>
		<div class="input"><?=$this->form->field('email', array('label' => 'E-mail', 'id' => 'email_input', 'placeholder' => 'Will be their login'));?><div class="input_help_right">Please enter their e-mail address.</div></div>
		<div class="input"><?=$this->form->field('password', array('type' => 'password', 'label' => 'Password', 'id' => 'password_input', 'placeholder' => 'Not your dog\'s name'));?><div class="input_help_right">Choose a password at least 6 characters long.</div></div>
		<div class="input"><?=$this->form->field('password_confirm', array('type' => 'password', 'label' => 'Confirm Password', 'id' => 'password_confirm_input'));?><div class="input_help_right">Just to be sure, type your password again.</div></div>
		<p><em><strong>Note:</strong> There will be no e-mail sent to this user. You must let them know what their password is.<br />This will change in the future after the testing phase.</em></p>
		<div class="submit-form">
			<?=$this->html->link('Cancel', array('controller' => 'users', 'action' => 'index')); ?> <?=$this->form->submit('Update User', array('class' => 'greenButton')); ?>
		</div>
</div>

<div class="grid_4">
	<div class="box">
		<p>
			<strong>Administrator</strong><br />
			These users can create and update other users and have complete access to everything.<br /><br />
			<strong>Content Editor</strong><br />
			These users can create and update things, but can not create any new users.<br /><br />
			<strong>Registered User</strong><br />
			These users have, more or less, "read-only" access and can only view certain things.
		</p>
	</div>
</div>

<?=$this->form->end(); ?>
<div class="clear"></div>
<br />
<script type="text/javascript">
$(function () {
	$("#UserCustomerAccessLevel").selectbox();
});

    function submitCheck() {
	if(($('#password_input').val() != $('#password_confirm_input').val())) {
	    $('#password_confirm_input').parent().siblings('.input_help_right').hide();
	    $('#password_confirm_error').remove();
	    $('#password_confirm_input').parent().parent().append('<div class="input_error_right error" id="password_confirm_error">Passwords must match.</div>');
	    return false;
	}
	return true;
    }
    $(document).ready(function() {
	$('#password_input').val('');
        
        $('input').blur(function() {
			$('.input_help_right').hide();
			
			if($('#email_input').val().length < 5) {
				$('#email_error').remove();
				$('#email_input').parent().parent().append('<div class="input_error_right error" id="email_error">You must provide their e-mail.</div>');
			}
			if($('#password_input').val().length < 6 && $('#password_input').val().length > 0) {
				$('#password_error').remove();
				$('#password_input').parent().parent().append('<div class="input_error_right error" id="password_error">Password must be at least 6 characters long.</div>');
			}
			$('.input_help_right').hide();
			//$(this).siblings('.error').show();
			$(this).siblings('#email_error').show();
			$(this).siblings('#password_error').show();
			$(this).siblings('#password_confirm_error').show();
        });
        
        $('input').focus(function() {
            $(this).parent().siblings().show();
            $(this).parent().siblings('.error').hide();
            $(this).parent().siblings('#email_error').hide();
            $(this).parent().siblings('#password_error').hide();
			$(this).parent().siblings('#password_confirm_error').hide();
        });
        
        $('#email_input').change(function() {
	    $.get('/users/is_email_in_use/' + $('#email_input').val(), function(data) {
                if(data == 'true') {
                    $('#email_error').remove();
                    $('#email_input').parent().parent().append('<div id="email_error">Sorry, this e-mail address is already registered.</div>');
                } else {
                    $('#email_error').remove();
                }
            });
        });
        
    });
</script>