$(document).ready(function() {
	$('a.set-user-status').live('click', function() {
		var user = $(this).attr('rel');
		setUserStatus(user.substring(2), user.substring(0,1));
	});
});

/**
 * Sets a user's status to either true or false.
 * This prevents a user from logging in.
 * 
 * @param id The user's MongoId
 * @param status What to set the active field to, 0 or 1 for true or false ("false" also works)
*/
function setUserStatus(id, status) {
	if(typeof(id) == 'undefined' || typeof(status) == 'undefined') {
		return false;
	}
	
	$.ajax({
		type: 'GET',
		url: '/admin/users/set_status/' + id + '/' + status + '.json',
		success: function(response) {
			// If the user's status was successfully changed
			if(response.success == true) {
				$('span.user-' + id).removeClass('active-dot');
				$('span.user-' + id).removeClass('inactive-dot');

				if(status == 1 || status == 'true') {
					$('span.user-' + id).addClass('active-dot');
					$('a.user-' + id).text('Disable');
					$('a.user-' + id).attr('rel', '0_' + id);
				} else {
					$('span.user-' + id).addClass('inactive-dot');
					$('a.user-' + id).text('Enable');
					$('a.user-' + id).attr('rel', '1_' + id);
				}
			}
		},
		error: function() {
			// failed
		},
		dataType: 'json'
	});
}