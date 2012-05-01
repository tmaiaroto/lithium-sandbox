<div class="grid_16">
	<h2 id="page-heading"><?=$display_name; ?></h2>
</div>

<div class="clear"></div>

<div class="grid_12">
	<p>
		<?=$this->html->link('Create a New Document...', array('admin' => true, 'controller' => 'examples', 'action' => 'create')); ?>
	</p>
	<table>
		<thead>
			<tr>
				<th class="left">Name</th>
				<th>Owner</th>
				<th>Created</th>
				<th class="right">Actions</th>
			</tr>
		</thead>
		<?php 
		$i=1;
		foreach($documents as $document) { 
			$alt = ($i % 2 == 0) ? 'alt':'';
			$i++;
		?>
		<tr>
			<td class="<?=$alt;?>">
				<?=$this->html->link($document->name, array('controller' => 'examples', 'action' => 'read', 'args' => array($document->url))); ?>
			</td>
			<td class="<?=$alt;?>">
				<?=$document->owner_id; ?>
			</td>
			<td class="<?=$alt;?>">
				<?=$this->html->date($document->created->sec); ?>
			</td>
			<td class="<?=$alt;?>">
				<?=$this->html->link('Edit', array('controller' => 'examples', 'action' => 'update', 'args' => array($document->url))); ?> | 
				<?=$this->html->link('Delete', array('controller' => 'examples', 'action' => 'delete', 'args' => array($document->url)), array('onClick' => 'return confirm(\'Are you sure you want to delete ' . $document->name . '?\')')); ?>
			</td>
		</tr>
		<?php } ?>
	</table>

<?=$this->Paginator->paginate(); ?>
<em>Showing page <?=$page; ?> of <?=$total_pages; ?>. <?=$total; ?> total record<?php echo ((int) $total > 1 || (int) $total == 0) ? 's':''; ?>.</em>
</div>

<div class="grid_4">
	<div class="box">
		<h2>Search for Documents</h2>
		<div class="block">
			<?=$this->html->query_form(array('label' => 'Query ')); ?>
		</div>
	</div>
</div>

<div class="clear"></div>