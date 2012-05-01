/*
 * Adds another criteria input field so multiple tags can be added.
 *
*/
function addCriteria(type, data) {
    var input_div_id = 'tags_criteria';
    var input_name = 'tags[]';
    var criteria_type = 'tag';
	
	// An example for another type if so desired.
	// Note: you need to ensure the model schema has the field(s) defined too.
    if(type == 'category' || type == 'categories') {
        input_div_id = 'category_criteria';
        input_name = 'categories[]';
        criteria_type = 'category';
    }
	
    var last_row = $('#' + input_div_id + ' div:last').attr('id');
	var current = 0;
	if(typeof(last_row) != 'undefined') {
		current = last_row.split('_'); // key 1 will be the value to increment
		current = parseInt(current[3]) + 1;
	}
	
	var i = current;
	// Add the form field (either populated or empty)
	if(typeof(data) == 'string') {
		$('#' + input_div_id).append('<div class="criteria_row" id="criteria_'+criteria_type+'_row_'+i+'"><input autocomplete="off" type="text" name="'+input_name+'" value="'+data+'" /><a href="#" tabindex="-1" onClick="$(\'#criteria_'+criteria_type+'_row_'+i+'\').remove(); return false;" class="criteria_input_remove_button">remove</a></div></div>');
	} else {
        $('#' + input_div_id).append('<div class="criteria_row" id="criteria_'+criteria_type+'_row_'+i+'"><input autocomplete="off" type="text" name="'+input_name+'" /><a href="#" tabindex="-1" onClick="$(\'#criteria_'+criteria_type+'_row_'+i+'\').remove(); return false;" class="criteria_input_remove_button">remove</a></div></div>');
    }
	
}

$(document).ready(function() {
	// This will automatically add new rows when tab is pressed, but also when a user clicks to another area...
	// It's nice with the tab, but it's annoything. Leave it off for now.
	/*
	$('#tags_criteria input:last').live('change', function(){
		addCriteria('tag');
		$("#tags_criteria .criteria_row:last input:first").focus();
	});
    
	*/
});