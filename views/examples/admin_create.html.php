<div class="grid_16">
	<h2 id="page-heading">Create New Example Document</h2>  
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
				<label for="tags_input">Tags Document</label>
				<div class="criteria_row">
					<input type="text" name="tags[]" id="tags_input" /> <a href="#" onclick="addCriteria('tag')">Add</a>
				</div>
			</div>
		</div>
		
		<?=$this->form->submit('Add Document', array('class' => 'greenButton')); ?> <?=$this->html->link('Cancel', array('controller' => 'examples', 'admin' => true, 'action' => 'index')); ?>
	</fieldset>
	
</div>

<div class="grid_4">
	<div class="box">
		<h2>Options</h2>
		<div class="block">
			
		</div>
	</div>
</div>

<?=$this->form->end(); ?>
<?=$this->html->script(array('manage_document_criteria.js'), array('inline' => true)); ?>
<div class="clear"></div>