<div class="grid_16">
	<h2 id="page-heading">Update Example Document</h2>  
</div>
<div class="clear"></div>

<div class="grid_12">
	<?=$this->form->create($document); ?>
	<fieldset class="admin">
		<legend>Primary Information</legend>
	    <?php
		foreach($fields as $k => $v) {
			// if the 'form' key is set we can use this helper. 'position' is optional.
			if((isset($v['form']['position']) && $v['form']['position'] != 'options') || (!isset($v['form']['position']) && isset($v['form']))) {
				echo $this->form->field($k, $v['form']);
				// 'help_text' is also optional.
				if(isset($v['form']['help_text'])) {
					echo '<div class="help_text">' . $v['form']['help_text'] . '</div>';
				}
			} 
		}
		?>
		
		<div class="criteria_column">
			<div id="tags_criteria">
				<label for="tags_input">Tags for this Document</label>
				<div class="criteria_row">
					<input type="text" name="tags[]" value="<?php echo isset($document['tags'][0]) ? $document['tags'][0]:''; ?>" id="tags_input" /> <a href="#" onclick="addCriteria('tag')">Add</a>
					<?php
					if(count($document['twitter_accounts']) > 1) {
						echo '<script type="text/javascript">';
						echo '$(document).ready(function() { ';
						$i=0;
						foreach($document['tags'] as $item) {
							if($i > 0) {
								echo 'addCriteria("tag", "' . $item . '");';
							}
							$i++;
						}
						echo '});';
						echo '</script>';
					}
					?>
				</div>
			</div>
		</div>
		
	    <?=$this->form->submit('Update Document'); ?> <?=$this->html->link('Cancel', array('controller' => 'examples', 'action' => 'index')); ?>
	</fieldset>
	
</div>

<div class="grid_4">
    <div class="box">
        <h2>Options</h2>
	    <div class="block">
			<fieldset class="admin">
			<?php
			foreach($fields as $k => $v) {
				if(isset($v['form'])) {
					if(isset($v['form']['position']) && $v['form']['position'] == 'options') {
						echo $this->form->field($k, $v['form']);
						if(isset($v['form']['help_text'])) {
							echo '<div class="help_text">' . $v['form']['help_text'] . '</div>';
						}
					}
				}
			}
			?>
			</fieldset>
        </div>
    </div>
</div>

<?=$this->form->end(); ?>
<?=$this->html->script(array('/js/manage_document_criteria.js'), array('inline' => true)); ?>
<div class="clear"></div>