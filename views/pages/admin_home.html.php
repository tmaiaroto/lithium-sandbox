<?php
use lithium\security\Auth;
$user = Auth::check('user');
?>
<div class="grid_16">
    <h2 id="page-heading">Admin Dashboard</h2>
</div>

<div class="clear"></div>

<div class="grid_12">
	<p>
		Welcome to the admin dashboard.
	</p>
</div>

<div class="grid_4">
    <div class="box">
	<h2>Actions</h2>
		<div class="block">
			<p>
				<?=$this->html->link('Logout', array('controller' => 'users', 'action' => 'logout')); ?>
			</p>
		</div>
    </div>
</div>

<div class="clear"></div>