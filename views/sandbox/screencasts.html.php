<?php $this->title('Tutorials'); ?>

<div class="grid_16">
	<div class="large_search">
		<?=$this->html->query_form(array('label' => '', 'button_copy' => 'Search')); ?>
	</div>
</div>

<div class="clear"></div>

<div class="grid_12 item-index-list">
	<ul>
		<?php 
		$i=1;
		foreach($documents as $document) { 
			$alt = ($i % 2 == 0) ? 'alt':'';
			$i++;
		?>
		<li class="<?=$alt; ?>">
			<span class="item-title"><?=$this->html->link($document->title, $document->link, array('target' => '_blank')); ?></span>
			<span class="item-description"><?php echo $document->description; ?></span>
			<?php
			/** TODO
			<div class="item-tags">
				<?php 
				foreach($document->tags as $tag) {
					echo '<span class="item-tag">' . $tag . '</span>';
				}
				?>
			</div> 
			 */
			?>
			<span class="item-date">Linked here on <?=$this->html->date($document->created->sec, 'F jS, Y'); ?></span>
		</li>
		<?php } ?>
	</ul>
	
	<div class="pagination">
		<?=$this->Paginator->paginate(); ?>
		<em>Showing page <?=$page; ?> of <?=$total_pages; ?>. <?=$total; ?> total record<?php echo ((int) $total > 1 || (int) $total == 0) ? 's':''; ?>.</em>
	</div>
</div>

<div class="clear"></div>