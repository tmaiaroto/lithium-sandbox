<div id="center-box-bg">
	<div id="center-box-content">
		<h1>Welcome</h1><br />
		<p>This is the main application's static "home" page. This is subject to change based on the branch you are on.
			The main sandbox page, which contains all the information you need, is kept separate and is always located at: /sandbox/page</p>
		<p>You can <?=$this->html->link('click here', array('controller' => 'sandbox', 'action' => 'page', 'args' => array('home'))); ?> to go to the sandbox main page.</p>
		<p>This sandbox has a basic example user access system. If this is your first time using the application, the first user you register will become an admin.</p>
		<p>You can <?=$this->html->link('click here', array('controller' => 'users', 'action' => 'register')); ?> to register a user.</p>
		<p>Then you can access the admin dashboard at /admin or by <?=$this->html->link('clicking here.', '/admin'); ?></p>
	</div>
</div>