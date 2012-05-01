<div class="grid_16">
	<h2 id="page-heading">Users</h2>
</div>

<div class="clear"></div>

<div class="grid_12">
	<table>
		<thead>
			<tr>
				<th class="left">E-mail</th>
				<th>Role</th>
				<th>Access Level</th>
				<th>Plan</th>
				<th>Created</th>
				<th class="right">Actions</th>
			</tr>
		</thead>
		<?php foreach($documents as $user) { ?>
		<tr>
			<td>
				<?php $active = ($user->active) ? 'active':'inactive'; ?>
				<?=$this->html->link($user->email, array('controller' => 'users', 'action' => 'read', 'admin' => true, 'args' => array($user->_id)), array('class' => 'user-info', 'title' => $user->first_name . ' ' . $user->last_name . ' / <em>' . $user->organization . '</em> (' . $active . ')')); ?>
			</td>
			<td>
				<?=$user->role; ?>
			</td>
			<td>
				<?=$user->customer_access_level; ?>
				<?php echo (empty($user->parent_id) && !empty($user->customer_access_level)) ? '*':''; ?>
			</td>
			<td>
				<?=$user->_plan->name; ?>
			</td>
			<td>
				<?=$this->html->date($user->created->sec); ?>
			</td>
			<td>
				<?=$this->html->link('Edit', array('controller' => 'users', 'action' => 'update', 'admin' => true, 'args' => array($user->_id))); ?> |
				<?=$this->html->link('Delete', array('controller' => 'users', 'action' => 'delete', 'admin' => true, 'args' => array($user->_id)), array('onClick' => 'return confirm(\'Are you sure you want to delete ' . $user->email . '?\')')); ?>
			</td>
		</tr>
		<?php } ?>
	</table>

<?=$this->Paginator->paginate(); ?>
<em>Showing page <?=$page; ?> of <?=$total_pages; ?>. <?=$total; ?> total record<?php echo ((int) $total > 1 || (int) $total == 0) ? 's':''; ?>.</em>
</div>

<div class="grid_4">
	<div class="box">
		<h2>Search for Users</h2>
		<div class="block">
			<?=$this->html->query_form(array('label' => 'Query ')); ?>
		</div>
	</div>
	<div class="box">
	<h2>Actions</h2>
	<div class="block">
		<p>
			<em>* Denotes account owner.</em>
		</p>
		<?=$this->html->link('Create New User', array('controller' => 'users', 'action' => 'create', 'admin' => true)); ?>
	</div>
	</div>
</div>

<div class="clear"></div>
<script type="text/javascript">
	$('.user-info').tipsy({gravity: 'sw', html: true});
</script>